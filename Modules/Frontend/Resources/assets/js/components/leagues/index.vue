<template>
  <section class="content-part bg-blue compitior_padding">
            <div class="container">
              <div class="row">
                <div class="col-sm-12">
                  <div class="compition-title text-center">
                    <h2>CREATE A COMPETITION</h2>
                  </div>
                </div>
                <div class="col-sm-6">
                    <div class="compition-part">
                      <div class="compition-tag">
                        <router-link to="/league/create/lms"><img src="/images/video1.jpg"></router-link>
                        <h2>LAST MAN<br>
                            STANDING</h2>
                      </div>
                      <router-link to="/league/create/lms"><div class="compition-caption white-bg caption-home">
                        <div v-b-tooltip.hover.right title="LAST MAN STANDING"><img class="lms_image_main" src="/images/icon23.png"></div>
                        <p>  You can’t pick the same team twice. If your team loses then you are out of the competition. <span>⟶</span></p>
                      </div>
                      </router-link>
                    </div>
                </div>
 
                <div class="col-sm-6">
                    <div class="compition-part">
                      <div class="compition-tag">
                        <router-link to="/league/create/lml"><img src="images/video2.jpg"></router-link>
                        <h2>LAST MAN<br>
                            LEAGUE</h2>
                      </div>
                      <router-link to="/league/create/lml">
                        <div class="compition-caption white-bg caption-home">
                          <div v-b-tooltip.hover.right title="LAST MAN LEAGUE"><img class="lml_image_main" src="/images/icon13.png"></div>
                          <p>You can’t pick the same team twice. You are awarded 3 points for win and one for a draw. <span>⟶</span></p>
                        </div>
                     </router-link>
                    </div>
                </div>

              </div>
            </div>
        </section>

      </template>