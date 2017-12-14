<?php
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
?>
<?php
/**
  * Formulário oculto
  * Data de criação : 23/05/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.04.08

    $Id: OCManterConfiguracao.php 65448 2016-05-23 18:05:46Z michel $
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once TCOM."TComprasConfiguracao.class.php";

function montSpanResponsaveis()
{
    $rsRecordSet = new RecordSet;

    $arResponsaveisEntidades = Sessao::read('arResponsaveisEntidades');

    if ( count ( $arResponsaveisEntidades ) > 0 ) {
        $rsRecordSet->preenche(  $arResponsaveisEntidades );
    }

    $obLista = new Lista;

    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Responsáveis pelos Departamentos de Compras das Entidades');

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código Entidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento ( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delResp');" );
    $obLista->ultimaAcao->addCampo("","&inId=[inId]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnResponsaveis').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnResponsaveis').innerHTML = '".$html."';\n";

    return $stJs;

}

function addResponsavel($inCodCGM, $inCodEntidade, $stNomCGM)
{
    $stJs = '';

    $arResponsaveisEntidades = Sessao::read('arResponsaveisEntidades');

    if ( is_array($arResponsaveisEntidades) ) {
        $stErro = '';
        foreach ($arResponsaveisEntidades as $registro) {
            if ($registro['cod_entidade'] == $inCodEntidade) {
                $stErro = 'Já existe um responsável para esta entidade.';
            }
        }
    }

    if ($stErro) {

        $stJs = "alertaAviso('$stErro','form','erro','".Sessao::getId()."');\n  ";

    } else {
        $inUltimoCodigoResp = Sessao::read('inUltimoCodigoResp');
        $arRegistro = array();
        $arRegistro['cod_modulo']   = 35;
        $arRegistro['parametro']    = 'responsavel';
        $arRegistro['valor']        = $inCodCGM;
        $arRegistro['exercicio']    = Sessao::getExercicio();
        $arRegistro['cod_entidade'] = $inCodEntidade;
        $arRegistro['nom_cgm']      = $stNomCGM;
        $arRegistro['inId']         = $inUltimoCodigoResp++;
        $arResponsaveisEntidades[] = $arRegistro;

        Sessao::write('arResponsaveisEntidades' , $arResponsaveisEntidades);
        $stJs = montSpanResponsaveis();
    }

    return $stJs;

}

function delResponsavel($inId)
{
    $arResponsaveisEntidades = Sessao::read('arResponsaveisEntidades');

    if ( count ($arResponsaveisEntidades) > 0 ) {

        foreach ($arResponsaveisEntidades as $registro) {

            if ($registro['inId'] == $inId) {
                $arExclusoes[] = $registro;
            } else {
                $arResps[] = $registro;
            }
        }

        $arResponsaveisEntidades = $arResps;
        $arResponsaveisEntidadesExcluidos = $arExclusoes;

        Sessao::write('arResponsaveisEntidades', $arResponsaveisEntidades);
        Sessao::write('arResponsaveisEntidadesExcluidos', $arResponsaveisEntidadesExcluidos);

        $stJs = montSpanResponsaveis();
    }

    return $stJs;
}

function validaDtFixa(Request $request)
{
    $stJs = "";
    $stTipo = "";
    $stRequest = "";
    
    foreach( $request->getAll() AS $key => $value ){
        if(strpos($key, 'stDtSolicitacao')!==FALSE){
            list ( $stRequest, $inCodEntidade, $inLinha ) = explode("_", $key);
        }
        if(strpos($key, 'stDtCompraDireta')!==FALSE){
            list ( $stRequest, $inCodEntidade, $inLinha ) = explode("_", $key);
        }

        if($stRequest != ''){
            $inNumCgm = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$inCodEntidade." and exercicio = '".Sessao::getExercicio()."'");
            $stNomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$inNumCgm);

            $request->set($stRequest     , $value);
            $request->set('inCodEntidade', $inCodEntidade);
            $request->set('stNomEntidade', $stNomEntidade);
            $request->set('inLinha'      , $inLinha);
            $request->set('stId'         , $key);

            break;
        }
    }

    //strpos

    if($request->get('stDtSolicitacao')){
        list ( $dia, $mes, $ano ) = explode("/", $request->get('stDtSolicitacao'));
        if($ano == Sessao::getExercicio()){
            include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";

            $obTComprasSolicitacao = new TComprasSolicitacao();
            $obTComprasSolicitacao->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obTComprasSolicitacao->setDado('exercicio', Sessao::getExercicio());
            $obTComprasSolicitacao->recuperaRelacionamentoSolicitacao($rsSolicitacao, "", "ORDER BY solicitacao.timestamp DESC LIMIT 1");

            if (!$rsSolicitacao->eof()) {
                $stMaxDtSolicitacao = $rsSolicitacao->getCampo('data');

                if(!SistemaLegado::comparaDatas($request->get('stDtSolicitacao'), $stMaxDtSolicitacao, TRUE))
                    $stMensagem = "A Data Fixa para Solicitação não pode ser inferior a data: ".$stMaxDtSolicitacao." (data da última solicitação), para a Entidade (".$request->get('inCodEntidade')." - ".$request->get('stNomEntidade').")";
            }
        }else
            $stMensagem = "A Data Fixa para Solicitação deve ser do exercício de ".Sessao::getExercicio()."!";

        if($stMensagem){
            $stJs .= "jQuery('#".$request->get('stId')."').val('');";
            $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."'); \n";
        }
    }

    if($request->get('stDtCompraDireta')){
        list ( $dia, $mes, $ano ) = explode("/", $request->get('stDtCompraDireta'));
        if($ano == Sessao::getExercicio()){
            include_once TCOM."TComprasCompraDireta.class.php";

            $obTCompraDireta = new TComprasCompraDireta;
            $obTCompraDireta->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obTCompraDireta->setDado('exercicio_entidade', Sessao::getExercicio());
            $obTCompraDireta->recuperaDataCompraDireta($rsCompraDireta, "", "ORDER BY compra_direta.timestamp DESC LIMIT 1");

            if (!$rsCompraDireta->eof()) {
                $stMaxDtSolicitacao = $rsCompraDireta->getCampo('data');

                if(!SistemaLegado::comparaDatas($request->get('stDtCompraDireta'), $stMaxDtSolicitacao, TRUE))
                    $stMensagem = "A Data Fixa para Compra Direta não pode ser inferior a data: ".$stMaxDtSolicitacao." (data da última compra direta), para a Entidade (".$request->get('inCodEntidade')." - ".$request->get('stNomEntidade').")";
            }
        }else
            $stMensagem = "A Data Fixa para Compra Direta deve ser do exercício de ".Sessao::getExercicio()."!";

        if($stMensagem){
            $stJs .= "jQuery('#".$request->get('stId')."').val('');";
            $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."'); \n";
        }
    }

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "recuperaFormularioAlteracao":

    break;

    case 'incluirResponsavel':
        $stJs = addResponsavel( $request->get('inCGM'), $request->get('inCodEntidade'), $request->get('stNomCGM') );
    break;

    case 'delResp':
        $stJs = delResponsavel( $request->get('inId') );
    break;

    case 'validaDtFixa':
        $stJs = validaDtFixa( $request );
    break;

}

if ($stJs) {
    echo $stJs;
}
