/**
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/**
* Funções
* Data de Criação: 05/02/2007


* @author Analista: Lucas Stephanou  
* @author Desenvolvedor: Lucas Stephanou

$Revision: 19882 $
$Name$
$Author: cassiano $
$Date: 2007-02-06 13:34:20 -0200 (Ter, 06 Fev 2007) $

Casos de uso: uc-01.01.00
*/

/* Ajax.Responders.register({
    onUninitialized : imagem(),
    onComplete : imagem()
  });
  */
  
function entere(value,conteiner,id,e){
	if ( e.keyCode == 13 ){
		document.getElementById(id).innerHTML = "<span ondblclick=\"returnEdit('"+conteiner+"','"+value+"','"+id+"');\" >"+value+"</span>";
	}else{
		return false;
	}
}
function returnEdit(conteiner,value,id){
	input = "<input type=\"text\" onkeyup=\"entere(this.value,'"+conteiner+"','"+id+"',event)\" value=\""+value+" \" />";
	document.getElementById(conteiner).innerHTML = input;
	document.getElementById(conteiner).firstChild.select();
}

function hiddenController(table,control,arquivo){
	rowSize = document.getElementById(table).rows.length/2;
	for (i=1;i<rowSize;i++){
		if (!control){
			TableTreeReq( table + '_row_' + i , arquivo);	
		}
		else{
			TableTreeLineControl(table+'_row_'+i , 'none' , '' , 'none');	
		}
	}
	if (!control){
		document.getElementById(table+'_openAll').style.display = 'none';
		document.getElementById(table+'_closeAll').style.display = '';
	}
	else{
		document.getElementById(table+'_openAll').style.display = '';
		document.getElementById(table+'_closeAll').style.display = 'none';
	}
	return false;
}

function TableTreeLineControl( id , ValueSub, ValueMais,  ValueMenos){
	
	if ( ValueSub == '' ){
		document.getElementById(id+'_sub').style.display =ValueSub ;				
	}else{
		document.getElementById(id+'_sub').style.display =ValueSub ;						
	}
	document.getElementById(id+'_mais').style.display = ValueMais ;
	document.getElementById(id+'_menos').style.display = ValueMenos ;			
}


function TableTreeReq( id , arquivo){	
    // coloca imagem do ajax
    carregandoContainer(id);
	return new Ajax.Updater
				( 
					id+ '_sub_cell_2', arquivo ,
					{ 	
						onSuccess: TableTreeLineControl( id , '' , 'none' , '' )  ,						
						asynchronous:true,
						evalScripts:true 						
					} 
				);
}

function carregandoContainer(id){
    // captura imagem
    var loading = window.parent.frames["telaPrincipal"].document.getElementById("carregando");
    var src = loading.innerHTML;
    
    var src_loading = window.parent.frames["telaPrincipal"].document.getElementById("ajax_carregando").src;
    var alt_loading = window.parent.frames["telaPrincipal"].document.getElementById("ajax_carregando").alt;
    // cria imagem de loading dinamicamente
    var newLoading=document.createElement("img");
    newLoading.setAttribute('src' , src_loading );
    newLoading.setAttribute('alt' , alt_loading );
    document.getElementById(id+'_sub_cell_2').innerHTML = src;
}
