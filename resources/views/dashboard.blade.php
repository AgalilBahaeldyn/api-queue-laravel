<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://unpkg.com/vue@3.3.4/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data.min.js"></script>
</head>
<body>
    
    <div id="app">
        <h1 class="text-center mb-5">รายงานสรุปคนเข้าจองคิวในระบบ</h1>
        <div class="container my-5">
            <div class="row justify-content-center">
                {{-- <div class="col d-flex align-items-center"> --}}
                  
                        
                    <div class="col-5">
                        <label for="date" class="form-label">เริ่มต้น</label>
                      <div class="input-group date" id="datepicker">
                        <input type="date" class="form-control" id="date" @@change="checkdate()"  v-model="sdate"/>
                        <span class="input-group-append">
                          <span class="input-group-text bg-light d-block">
                            <i class="bi bi-calendar"></i>
                          </span>
                        </span>
                      </div>
                    </div>
                    
                    
                {{-- </div> --}}
                {{-- <div class="col d-flex align-items-center"> --}}
                  
                        
                    <div class="col-5">
                        <label for="date" class="form-label">สิ้นสุด</label>
                      <div class="input-group date" id="datepicker">
                        <input type="date" class="form-control" id="date" @@change="checkdate()" v-model="edate"/>
                        <span class="input-group-append">
                          <span class="input-group-text bg-light d-block">
                            <i class="bi bi-calendar"></i>
                          </span>
                        </span>
                      </div>
                    </div>
                  
                    
                {{-- </div> --}}
            </div>
        </div>

        <div class="container-fluid mt-10 px-5">
            <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">ลำดับ</th>
                    <th scope="col">เลขคิว</th>
                    <th scope="col">วันที่เข้าจอง</th>
                    <th scope="col">เลขบัตร</th>
                    <th scope="col">ชื่อ</th>
                    <th scope="col">อายุ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in datareport">
                    <th>@{{item.index}}</th>
                    <td>@{{item.catagory}}:@{{item.queue}}</td>
                    <td>@{{item.create_at}}</td>
                    <td>@{{item.cid}}</td>
                    <td>@{{item.fullname}}</td>
                    <td>@{{item.age}}</td>
                  </tr>
               
              
                </tbody>
              </table>
        </div>
        
    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        const { createApp } = Vue
      
        createApp({
          data() {
            return {
                sdate:moment().tz('Asia/Bangkok').format('YYYY-MM-DD'),
                edate:moment().tz('Asia/Bangkok').format('YYYY-MM-DD'),
              datareport:{},
              
              
            }
          },
          mounted() {
            this.checkdate()
          },

          methods: {
            
            checkdate(){
                var requestOptions = {
                method: 'GET',
                redirect: 'follow'
                };
                fetch(`https://wat.wseservice.com/api/v1/service/getreport/${this.sdate}/${this.edate}`, requestOptions)
                .then(response => response.json())
                .then(result => {
                    this.datareport=result

                    this.datareport.forEach((e,i) => {
                        e.index=i+1
                        e.create_at=moment(e.create_at, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");

                    });
                console.log(this.datareport)
                })
                .catch(error => console.log('error', error));

            }
          },        
        }).mount('#app')
      </script>
</body>
</html>