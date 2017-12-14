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
    * Página de JavaScript para Consulta de Cadastro Economico
    * Data de Criação: 16/09/2005


    * @author Marcelo B. Paulino

    * @ignore

	* $Id: JSConsultarCadastroEconomico.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.02.21

*/

/*
$Log$
Revision 1.9  2007/04/23 18:28:29  dibueno
*** empty log message ***

Revision 1.8  2007/03/15 14:26:24  cercato
alterando formulario para apresentar a situacao na lista de licencas.

Revision 1.7  2007/03/05 13:11:59  dibueno
Bug #7676#

Revision 1.6  2007/03/02 14:44:48  dibueno
Bug #7676#

Revision 1.5  2007/02/13 16:00:43  rodrigo
#6255#

Revision 1.4  2006/11/20 09:54:18  cercato
bug #7438#

Revision 1.3  2006/09/15 14:32:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>

<script type="text/javascript">

function preencheProxCombo( inPosicao  ){
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = 'telaPrincipal';
}

function preencheCombosAtividade(){
    document.frm.stCtrl.value = 'preencheCombosAtividade';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = 'telaPrincipal';
}

function buscaValor( tipoBusca ){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = 'telaPrincipal';
}

function Limpar(){
    document.getElementById("stNomeNatureza").innerHTML = "&nbsp;";
    document.getElementById("stNomeSocio").innerHTML    = "&nbsp;";
    document.getElementById("stEndereco").innerHTML     = "&nbsp;";

}

function Cancelar(){
<?php
    $link = Sessao::read( "link" );
     $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function visualizarAtividade(tipo_processo,cod_atividade){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipo_processo;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodAtividade='+cod_atividade;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function visualizarElemento(tipo_processo,cod_elemento,nom_elemento){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipo_processo;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodElemento='+cod_elemento+'&stNomElemento='+nom_elemento;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function visualizarLicenca(tipo_processo,cod_licenca,especie){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipo_processo;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodLicenca='+cod_licenca+'&stEspecie='+especie;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function imprimirLicenca ( tipo_processo, inscricao_economica, cod_licenca, cod_documento , cod_tipo_documento, exercicio, tipo_licenca, nome_arquivo_template, nome_documento, situacao ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    var stOrigem = 'consulta';

    if ( situacao != "Ativa" ) { 
        mensagem = "@A lincença precisa esta ativa.";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }else {
        document.frm.target = 'oculto';
        document.frm.stCtrl.value = tipo_processo;
        document.frm.action = '<?=$pgImprimirLicenca;?>?<?=Sessao::getId();?>&stOrigemFormulario='+stOrigem+'&inCodLicenca='+cod_licenca+'&inCodigoDocumento='+cod_documento+'&inCodigoTipoDocumento='+cod_tipo_documento+'&inInscricaoEconomica='+inscricao_economica+'&inExercicio='+exercicio+'&stTipoLicenca='+tipo_licenca+'&stNomeArquivoTemplate='+nome_arquivo_template+'&stNomDocumento='+nome_documento;
        document.frm.submit();
        document.frm.target = 'telaPrincipal';
        document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    }
}

function visualizarProcesso(cod_processo, ano_exercicio, ocorrencia_atividade, inscricao_economica){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'visualizarProcesso';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodProcesso='+cod_processo+'&inAnoExercicio='+ano_exercicio+'&inOcorrenciaAtividade='+ocorrencia_atividade+'&inInscricaoEconomica='+inscricao_economica;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function relatorio(){
    var link = new String();

    link ="<?=CAM_FW_POPUPS?>relatorio/OCRelatorio.php?<?=Sessao::getId()?>";
    link+="&inNumInscricaoEconomicaInicial="+relatorio.arguments[0];
    link+="&stCaminho="+document.forms[0].stCaminho.value;
    link+="&stTipoRelatorio=analitico";
    link+="&stAcao=Incluir";
    link+="&inNumCopias=1";
    link+="&stTipoRelatorioSubmit=analitico";
    link+="&stSituacao="+relatorio.arguments[1];

    window.parent.frames["oculto"].document.location.href=link;
}

</script>