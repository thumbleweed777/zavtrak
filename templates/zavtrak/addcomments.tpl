<div id="add_comment">
            <h3>Оставить комментарий</h3>
[not-logged]
    <div class="imia">
        <div class="imia-l">Ваше имя: </div>
        <div class="imia-r"><input type="text" name="name" id="name" class="f_input" /></div>
    </div>
    <div class="email">
        <div class="email-l">Ваш email: </div>
        <div class="email-r"><input type="text" name="mail" id="mail" class="f_input" /></div>
    </div> 
 [/not-logged]   
    <div class="ctr"></div>    
            <div class="kom"> 
                [not-wysywyg]<textarea name="comments"   class="f_textarea" onclick="setNewField(this.name, document.getElementById( 'dle-comments-form' ))" />{text}</textarea>[/not-wysywyg]{wysiwyg}
              </div>
<div class="ctr"></div>   
        <div class="b-l">
            [sec_code]
            <div class="v-v">Введите код:</div> 
            <div class="cod"> {sec_code}</div> 
            <div class="input"><input type="text" name="sec_code" id="sec_code" style="width:106px" class="f_input2" /></div>
            [/sec_code]
        </div> 
        <div class="b-r">
            <input onclick="doAddComments();return false;" name="submit" type="submit" class="s_button"  value="Отправить"  />               
        </div>  
</div>
<div class="ctr"></div> </div> 