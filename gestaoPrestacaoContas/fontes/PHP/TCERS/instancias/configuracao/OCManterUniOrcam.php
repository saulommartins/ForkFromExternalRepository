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
 * Página Oculta - Parâmetros do Arquivo UNIORCAM.
 * Data de Criação   : 11/02/2005

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Diego Lemos de Souza

 * @ignore

 * Casos de uso: uc-02.08.05

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EXP_NEGOCIO . "RExportacaoTCERSArqUniOrcam.class.php";

$stCtrl = $request->get('stCtrl');

$obRExportacaoTCERSArqUniOrcam = new RExportacaoTCERSArqUniOrcam();
$obRExportacaoTCERSArqUniOrcam->obRExportacaoTCERSUniOrcam->setExercicio(Sessao::getExercicio());
$obRExportacaoTCERSArqUniOrcam->obRExportacaoTCERSUniOrcam->listar($rsUnidadeOrcamento);
$obRExportacaoTCERSArqUniOrcam->obRExportacaoTCERSUniOrcam->listarDadosConversao($rsUnidadeOrcamentoConversao);

// Acoes por pagina
switch ($stCtrl) {

    //monta HTML com os ATRIBUTOS relativos a Conta Contábil selecionada
    case "MontaListaUniOrcam":

        if ($rsUnidadeOrcamento->getNumLinhas() != 0) {
            $obLista = new Lista;
            $obLista->setMostraPaginacao( false );

            $obLista->setRecordSet( $rsUnidadeOrcamento );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 3 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Orgão" );
            $obLista->ultimoCabecalho->setWidth( 25 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Unidade" );
            $obLista->ultimoCabecalho->setWidth( 25 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Identificador" );
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "CGM" );
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "[num_orgao] - [nom_orgao]" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "[num_unidade] - [nom_unidade]" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            // Define Objeto Combo para identificadores
            $obRExportacaoTCERSArqUniOrcam->listarIdentificador($rsIdentificador);
            $obCmbIdentificador = new Select();
            $obCmbIdentificador->setName       ("inIdentificador_[num_orgao]_[num_unidade]_"   );
            $obCmbIdentificador->setRotulo     (""                                             );
            $obCmbIdentificador->addOption     ("","Selecione"                                 );
            $obCmbIdentificador->setCampoId    ("identificador"                                );
            $obCmbIdentificador->setCampoDesc  ("[cod_identificador] - [nom_identificador]"    );
            $obCmbIdentificador->preencheCombo ($rsIdentificador                               );
            $obCmbIdentificador->setNull       ( false                                         );
            $obCmbIdentificador->setTitle      ("Selecione o identificador"                    );
            $obCmbIdentificador->setValue      ("identificador");

            $obLista->addDadoComponente( $obCmbIdentificador );
            $obLista->ultimoDado->setCampo( "identificador" );
            $obLista->commitDadoComponente();

            //Define objeto BuscaInner para cgm
            $obBscCGM = new BuscaInner;
            $obBscCGM->setRotulo            ( "CGM"             );
            $obBscCGM->setTitle             ( "Selecione o CGM" );
            $obBscCGM->setNull              ( false             );
            $obBscCGM->obCampoCod->setName  ( "inNumCGM_"       );
            $obBscCGM->obCampoCod->setValue ( "numcgm"          );
            $obBscCGM->obCampoCod->obEvento->setOnChange("buscaCGM('juridica');" );
            $obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','','juridica','".Sessao::getId()."','800','550')" );

            $obLista->addDadoComponente( $obBscCGM );
            $obLista->commitDadoComponente();

            $obLista->montaHTML();
            $stHtml = $obLista->getHTML();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace("'","\\'",$stHtml);
        }
        // preenche a lista com innerHTML
        $stJs .= "d.getElementById('spnUniOrcam').innerHTML = '".$stHtml."';";

        if ($rsUnidadeOrcamentoConversao->getNumLinhas() != 0) {
            $obLista = new Lista;
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsUnidadeOrcamentoConversao );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 3 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Exercício" );
            $obLista->ultimoCabecalho->setWidth( 25 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Orgão" );
            $obLista->ultimoCabecalho->setWidth( 25 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Unidade" );
            $obLista->ultimoCabecalho->setWidth( 25 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Identificador" );
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "CGM" );
            $obLista->ultimoCabecalho->setWidth( 30 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "exercicio" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "num_orgao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "num_unidade" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            // Define Objeto Combo para identificadores
            $obRExportacaoTCERSArqUniOrcam->listarIdentificador($rsIdentificador);
            $obCmbIdentificador = new Select();
            $obCmbIdentificador->setName("inIdentificadorConversao_[num_orgao]_[num_unidade]_[exercicio]_");
            $obCmbIdentificador->setRotulo("");
            $obCmbIdentificador->addOption("","Selecione");
            $obCmbIdentificador->setCampoId("identificador");
            $obCmbIdentificador->setCampoDesc("[cod_identificador] - [nom_identificador]");
            $obCmbIdentificador->preencheCombo($rsIdentificador);
            $obCmbIdentificador->setNull( false );
            $obCmbIdentificador->setTitle("Selecione o identificador");
            $obCmbIdentificador->setValue("identificador");

            $obLista->addDadoComponente( $obCmbIdentificador );
            $obLista->ultimoDado->setCampo( "identificador" );
            $obLista->commitDadoComponente();

            //Define objeto BuscaInner para cgm
            $obBscCGM = new BuscaInner;
            $obBscCGM->setRotulo              ( "CGM"                          );
            $obBscCGM->setTitle               ( "Selecione o CGM"              );
            $obBscCGM->setNull                ( false                          );
            $obBscCGM->obCampoCod->setName    ( "inNumCGMConversao_"           );
            $obBscCGM->obCampoCod->setValue   ( "numcgm"                       );
            $obBscCGM->obCampoCod->obEvento->setOnChange("buscaCGM('juridica');" );
            $obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMConversao','','juridica','".Sessao::getId()."','800','550')" );

            $obLista->addDadoComponente( $obBscCGM );
            $obLista->commitDadoComponente();

            $obLista->montaHTML();
            $stHtml = $obLista->getHTML();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace("'","\\'",$stHtml);
        }

        // preenche a lista com innerHTML
        $stJs .= "d.getElementById('spnUniOrcamConversao').innerHTML = '".$stHtml."';";

    break;
}

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}

SistemaLegado::LiberaFrames();

?>
