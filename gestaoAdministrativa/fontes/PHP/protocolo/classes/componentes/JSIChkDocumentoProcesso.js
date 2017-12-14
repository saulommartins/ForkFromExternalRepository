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
* Funções JavaScript para o componente ChkDocumentoProcesso
* Data de Criação: 13/10/2006


* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 17506 $
$Name$
$Author: cassiano $
$Date: 2006-11-08 14:43:54 -0200 (Qua, 08 Nov 2006) $

Casos de uso: uc-01.06.98
*/
?>
<script type="text/javascript">
function copiaDigital(cod, cod_processo, ano_processo){
    var x = 200;
    var y = 140;
    var sArq = '<?=CAM_GA_PROT_POPUPS."documento/FMDocumentoProcesso.php";?>?<?=Sessao::getId();?>&codDoc='+cod;

    if (cod_processo) {
        sArq += '&inCodProcesso='+cod_processo;
    }

    if (ano_processo) {
        sArq += '&stAnoProcesso='+ano_processo;
    }

    var wVolta=false;
    tela = window.open(sArq,'tela','titlebar=no,hotkeys=no,width=550px,height=320px,resizable=1,scrollbars=1,left='+x+',top='+y);
    window.tela.focus();
}

</script>
