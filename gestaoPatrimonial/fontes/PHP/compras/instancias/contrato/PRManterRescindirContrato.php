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
    * Data de Criação: 07/10/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @package URBEM
    * @subpackage

    * Casos de uso :
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoContrato.class.php";
include_once TLIC."TLicitacaoRescisaoContrato.class.php";
include_once TLIC."TLicitacaoRescisaoContratoResponsavelJuridico.class.php";

Sessao::getExercicio();
$stAcao = $request->get('stAcao');

$dadosFiltro = Sessao::read('dadosFiltro',$param);

foreach ($dadosFiltro as $chave => $valor) {
    
    if ( $chave == "inNumCGM" && is_array($valor)) {
        $valor = implode (',', $valor);
    }
    
    $stFiltro.= "&".$chave."=".$valor;
}

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
$stMensagem = "";

switch ($stAcao) {

case "rescindir":
    $obTLicitacaoContrato = new TLicitacaoContrato();
    $obTLicitacaoContrato->setDado('exercicio_contrato', $request->get('stExercicio'));
    $obTLicitacaoContrato->setDado('num_contrato', $request->get('inNumContrato'));
    $obTLicitacaoContrato->setDado('cod_entidade', $request->get('inCodEntidade'));
    $obTLicitacaoContrato->recuperaPorChave( $rsLicitacaoContrato );

    if ( implode(array_reverse(explode('/',$request->get('dtRescisao')))) < implode(array_reverse(explode('/',$request->get('dtAssinatura')))) ) {
        SistemaLegado::exibeAviso(urlencode("A data de rescisão não pode ser anterior que a data de assinatura do contrato"), "n_incluir", "erro" );

    } else {
        $obTLicitacaoRescisaoContrato = new TLicitacaoRescisaoContrato();
        $obTLicitacaoRescisaoContrato->recuperaProximoNumRescisao($rsLicitacaoRescisaoContrato);
        $obTLicitacaoRescisaoContrato->setDado('exercicio_contrato', $request->get('stExercicioContrato'));
        $obTLicitacaoRescisaoContrato->setDado('num_contrato', $request->get('inNumContrato'));
        $obTLicitacaoRescisaoContrato->setDado('cod_entidade', $request->get('inCodEntidade'));
        $obTLicitacaoRescisaoContrato->setDado('exercicio', Sessao::getExercicio());
        $obTLicitacaoRescisaoContrato->setDado('num_rescisao', $rsLicitacaoRescisaoContrato->getCampo("maximo"));
        $obTLicitacaoRescisaoContrato->setDado('dt_rescisao', $request->get('dtRescisao'));
        $obTLicitacaoRescisaoContrato->setDado('vlr_cancelamento', $request->get('vlCancelamento'));
        $obTLicitacaoRescisaoContrato->setDado('vlr_multa', $request->get('vlMulta'));
        $obTLicitacaoRescisaoContrato->setDado('vlr_indenizacao', $request->get('vlIndenizacao'));
        $obTLicitacaoRescisaoContrato->setDado('motivo', $request->get('stMotivo'));
        
        if($request->get('stCtrl') == 'inclusao')
            $obTLicitacaoRescisaoContrato->inclusao();
        else 
            $obTLicitacaoRescisaoContrato->alteracao();

        if ($request->get('inCodResponsavelJuridico')) {
            $obTLicitacaoRescisContrRespJuridico = new TLicitacaoRescisaoContratoResponsavelJuridico();
            $obTLicitacaoRescisContrRespJuridico->setDado('exercicio_contrato', $request->get('stExercicioContrato'));
            $obTLicitacaoRescisContrRespJuridico->setDado('num_contrato', $request->get('inNumContrato'));
            $obTLicitacaoRescisContrRespJuridico->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obTLicitacaoRescisContrRespJuridico->setDado('numcgm', $request->get('inCodResponsavelJuridico'));
            
            if($request->get('stCtrl') == 'inclusao')
                $obTLicitacaoRescisContrRespJuridico->inclusao();
            else 
                $obTLicitacaoRescisContrRespJuridico->alteracao();
        }

        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$stAcao.$stFiltro,"Contrato: ".$request->get('inNumContrato')."/".$request->get('stExercicioContrato'),"incluir", "aviso", Sessao::getId(),"");

    }

    break;
}

Sessao::encerraExcecao();

?>