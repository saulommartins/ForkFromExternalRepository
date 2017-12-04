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
    * JavaScript para o Formulario de Baixa de Notas Fiscais

    * Data de Criação   : 25/11/2008


    * @author Analista      : Heleno Santos
    * @author Desenvolvedor : Jâno Eduardo vasconcellos de Mgalhaes
    * @ignore

	* $Id: JSManterImpressao.js 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
?>
<script>
function verificaMaior(){
    var inicio = document.frm.inNotaFiscalInicial.value;
    var fim = document.frm.inNotaFiscalFinal.value;

    if (fim !=''){
        if (fim < inicio){
           
            document.frm.inNotaFiscalFinal.value = '';
            document.frm.inNotaFiscalFinal.focus();
         alertaAviso('Nota final menor que nota inicial()','form','erro','<?=Sessao::getId()?>');
        }
    }
}
</script>
