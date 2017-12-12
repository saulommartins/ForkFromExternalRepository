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
<?
/**
    * Arquivo JavaScript utilizado na Configuração do Orçamento
    * Data de Criação: 13/07/2004
    
    
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues
    
    * @package URBEM
    * @subpackage Regra
    
    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.01.01
*/

/*
$Log$
Revision 1.3  2006/07/05 20:42:45  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function validaValor() {    
    stCodEntidadePrefeitura = document.frm.inCodEntidadePrefeitura.value;
    stCodEntidadeCamara = document.frm.inCodEntidadeCamara.value;
    stCodEntidadeRPPS = document.frm.inCodEntidadeRPPS.value;
    if ( (( stCodEntidadePrefeitura == stCodEntidadeCamara) && (stCodEntidadePrefeitura!="") ) || (( stCodEntidadePrefeitura == stCodEntidadeRPPS)&&(stCodEntidadePrefeitura!="") ) || ( ( stCodEntidadeCamara == stCodEntidadeRPPS) && (stCodEntidadeCamara!="")) ){        
        return false;              
        
    }else{
        
        return true;              
    }         
}
function Salvar(){
    var mensagem   = "";
    if( Valida() ){        
        if ( validaValor() ){     
              document.frm.submit();
        }
        else {            
        
            mensagem += "@As entidades selecionadas para Prefeitura, Câmara e RPPS não podem se repetir.";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');             
        }            
    }
 }
 
function validaCampos(BuscaValor){
    document.frm.stCtrl.value = BuscaValor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function validaPorcentagem(nuValorPorcentagem){    
    var nuValor = parseFloat(nuValorPorcentagem.replace(",","."));
    if ( nuValor > 100) {
        jQuery('#nuLimiteSuplementacaoDecreto').val("100,00");
    }
}

function toFloat( strValor ) {
// Descrição: Garante retorno numérico para entradas de strings
// toFloat('-12,345') -> -12.345
// toFloat('') -> 0
// toFloat('12.3') -> 12.3
// toFloat('-12.3') -> -12.3
// toFloat() -> 0
// toFloat('12,3') -> 12.3
// toFloat('-12,3') -> -12.3
// toFloat('abc') -> 0
	if ( (strValor == null) || (strValor.length == 0) ) {
		return 0;
	}
	if (!isNaN(strValor)) {
		return parseFloat(strValor);
	}
	retorno = limpaParaMascara(strValor,'valores');
    procurado = /,/;
    retorno = retorno.replace(procurado, ".");
	if ( (retorno == "") || (isNaN(retorno)) ) {
		return 0;
	}
	return parseFloat(retorno);
}

</script>
