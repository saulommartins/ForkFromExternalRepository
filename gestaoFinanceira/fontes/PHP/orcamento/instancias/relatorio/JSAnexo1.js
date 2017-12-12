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
    * Arquivo JavaScript utilizado no Relatorio Anexo 1
    * Data de Criação   : 28/06/2005


    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.5  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado(BuscaValor){
    var stAction = document.frm.action;
    document.frm.stCtrl.value = BuscaValor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'oculto';
    document.frm.action = stAction; 
}
function validaValor() {
    inCodDemValores = document.frm.inCodDemValores.value;
    stDataInicial = document.frm.stDataInicial.value;
    stDataFinal = document.frm.stDataFinal.value;
    inCodDemDespesa =  document.frm.inCodDemDespesa.value;

    if ( inCodDemValores == "2" ) {
        if( ( stDataInicial == "" ) || ( stDataFinal == "" ) || ( inCodDemDespesa == "" ) ){
            return false;
        }
        else{
            return true;
        }
    }
    else{
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
            mensagem += "@Para o relatório de Balanço informe o tipo de Valor e Despesa!";

            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }
    }
 }

</script>
