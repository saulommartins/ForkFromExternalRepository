<script type="text/javascript">
/*
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
</script>
<?php
/**
    * Arquivo JavaScript
    * Data de Criação   : 10/11/2006


    * @author Analista: Gelson
    * @author Desenvolvedor: Lucas Stephanou

    * @ignore

    $Revision: 25570 $
    $Name$
    $Autor:$
    $Date: 2007-09-20 11:16:52 -0300 (Qui, 20 Set 2007) $

    * Casos de uso: uc-03.05.25
    
    * $Log$
    * Revision 1.9  2007/09/20 14:16:30  andre.almeida
    * Ticket#10087#
    *
    * Revision 1.8  2007/03/28 21:29:57  hboaventura
    * Bug #8940#
    *
    * Revision 1.7  2006/11/24 18:52:47  domluc
    * Manutenção de Proposta
    *
    * Revision 1.6  2006/11/16 12:52:11  domluc
    * Manutenção de Proposta
    *
    * Revision 1.5  2006/11/14 18:49:34  domluc
    * Atualizado COmentarios
    *
    * Revision 1.4  2006/11/14 18:48:02  domluc
    * Adicionada Tag Log no CVS
    *

*/
?>

<script type="text/javascript">

	// atualiza particpante
	// na proposta
	function selecionaParticipante( Objeto ){		
		var f = document.frm;
		for( i=0 ; i<f.elements.length ; i++) {			
            if( typeof(f.elements[i]) == 'object' ){               
				var idE = new String(f.elements[i].id);
				
                if( f.elements[i].id != Objeto.id && idE.substring(0,16) == 'rd_participante_'){
					f.elements[i].checked = false;
				}
            }
        }
        document.getElementById('spnDadosItem').innerHTML='';
		// atualiza na sessão participante selecionado
		parametro = '&participante='+Objeto.value;
		executaFuncaoAjax( 'setaParticipante', parametro, true );
	}
	
	/* atualizar qual foi selecionado  */
	function selecionaItem( Objeto ){
		var f = document.frm;
		for( i=0 ; i<f.elements.length ; i++) {			
			if( typeof(f.elements[i]) == 'object' ){
				var idE = new String(f.elements[i].id);
				if( f.elements[i].id != Objeto.id && idE.substring(0,8) == 'rd_item_'){
					f.elements[i].checked = false;
				}
			}
		}
		// atualiza na sessão item foi selecionado
		parametro = '&item='+Objeto.value;
		executaFuncaoAjax( 'setaItem', parametro, true );
	}

    function limparItem(){
        document.getElementById('dtValidade').value='';
        document.getElementById('inCodMarca').value='';
        document.getElementById('stNomMarca').innerHTML = '&nbsp;';
        document.getElementById('stValorUnitario').value='0,00';
        document.getElementById('stValorTotal').value='0,00';
        document.getElementById('dtValidade').focus();
    }
    
	function excluirParticipante( cgm ){		
		parametro = '&cgm_fornecedor='+cgm;
		executaFuncaoAjax( 'excluirParticipante', parametro, true );
	}
	
	function limpar(){		
		parametro = '';
		executaFuncaoAjax( 'excluirParticipante', parametro, true );
	}
	
	function limparParticipante(){
		document.getElementById('inCgmFornecedor').value = '';
		document.getElementById('stNomParticipante').innerHTML = '&nbsp;';
	}
    
    function limparImportarProposta() {
        document.getElementById('stArquivoImportacao').value = '';
    }

	var intervalo;
	var qtdVerificada = 0;
	
    function importarArquivoFornecedor(){
        var stCtrl      = document.frm.stCtrl.value;
        var stAcao      = document.frm.action;
        var stTarget    = document.frm.target;
        document.frm.btnImportar.disabled = true;
        document.frm.stCtrl.value = "uploadArquivo";
        document.frm.target = 'oculto';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.target = stTarget; 
        document.frm.action = stAcao;
        intervalo = window.setInterval("verificaTerminoUpload();", 1000 );
    }

    function verificaTerminoUpload() {
        if( qtdVerificada < 10 ) {
            executaFuncaoAjax( 'verificaTerminoUpload', 'stArquivoImportacao' );
            qtdVerificada += 1;
        } else {
            pararVerificaTerminoUpload();
            qtdVerificada = 0;
        }
    }
    
    function pararVerificaTerminoUpload() {
        clearInterval(intervalo);
        document.frm.btnImportar.disabled = false;
    }
    
	
</script>
