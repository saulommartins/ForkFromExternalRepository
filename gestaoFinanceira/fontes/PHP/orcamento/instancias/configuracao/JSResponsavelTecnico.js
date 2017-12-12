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

function buscaCGM(){
    document.frm.stCtrl.value = 'buscaCGM';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaCGM_Filtro(){
    document.frm.stCtrl.value = 'buscaCGM';
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    
}

function buscaConselho(){
    if(( document.frm.inCodigoProfissao.value != '25' ) && (document.frm.inCodigoProfissao.value != '45')){
        alertaAviso('@Profissão inválida!( '+document.frm.inCodigoProfissao.value+' )','form','erro','<?=Sessao::getId();?>');
        return false;
    } else {
        document.frm.stCtrl.value = 'buscaConselho';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }
}

function Cancelar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>?<?=Sessao::getId();?>&stAcao=<?=$stAcao;?>&pg=<?=$_GET["pg"]?>&pos=<?=$_GET["pos"]?>';
}
function Limpar(){
    buscaValor('limpaTudo');
}
function buscaValor(BuscaValor){
    stTmpTarget = document.frm.target;
    stTmpAction = document.frm.action;
    document.frm.stCtrl.value = BuscaValor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTmpTarget;
    document.frm.action = stTmpAction;
}
</script>
