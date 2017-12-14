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
    * Página de processamento do formulário da Gráfica

    * Data de Criação   : 26/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: PRManterImpressao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISAutorizacaoNotas.class.php"                                     );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISAutorizacaoDocumento.class.php"                                 );
include_once( CAM_GT_FIS_VISAO."VFISEmitirAutorizacaoImpressao.class.php"                                 );

$obVisao = new VFISEmitirAutorizacaoImpressao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterImpressao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

switch ($_REQUEST['stAcao']) {
    case 'incluir':

        $obTAutorizacaoNotas     = new TFISAutorizacaoNotas();
        $obTAutorizacaoDocumento = new TFISAutorizacaoDocumento();

        $stFiltro = " WHERE inscricao_economica = ".$_REQUEST['inInscricaoEconomica']." AND nota_inicial BETWEEN ".$_REQUEST['inNotaFiscalInicial']." AND ".$_REQUEST['inNotaFiscalFinal']." AND nota_final BETWEEN ".$_REQUEST['inNotaFiscalInicial']." AND ".$_REQUEST['inNotaFiscalFinal'];
        $obTAutorizacaoNotas->recuperaTodos( $rsNotas, $stFiltro );
        if ( !$rsNotas->Eof() ) {
            SistemaLegado::exibeAviso( "Inscrição Econômica (".$rsNotas->getCampo("inscricao_economica").") já possui autorização para notas ".$rsNotas->getCampo("nota_inicial")." até ".$rsNotas->getCampo("nota_final").".", "n_erro", "erro" );
            exit;
        }

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTAutorizacaoNotas );

            $obTAutorizacaoNotas->proximoCod( $inNumAutorizacao );

            $obTAutorizacaoNotas->setDado( "cod_autorizacao"    , $inNumAutorizacao                 );
            $obTAutorizacaoNotas->setDado( "numcgm"             , $_REQUEST['inCGM']                );
            $obTAutorizacaoNotas->setDado( "inscricao_economica", $_REQUEST['inInscricaoEconomica'] );
            $obTAutorizacaoNotas->setDado( "numcgm_usuario"     , Sessao::read('numCgm')                   );
            $obTAutorizacaoNotas->setDado( "serie"              , $_REQUEST['stSerie']              );
            $obTAutorizacaoNotas->setDado( "qtd_taloes"         , $_REQUEST['inQuantidadeTaloes']   );
            $obTAutorizacaoNotas->setDado( "nota_inicial"       , $_REQUEST['inNotaFiscalInicial']  );
            $obTAutorizacaoNotas->setDado( "nota_final"         , $_REQUEST['inNotaFiscalFinal']    );
            $obTAutorizacaoNotas->setDado( "qtd_vias"           , $_REQUEST['inQuantidadeVias']     );
            $obTAutorizacaoNotas->setDado( "observacao"         , $_REQUEST['stObservacoes']        );

            $obTAutorizacaoNotas->inclusao();

            $obTAutorizacaoDocumento->setDado( "cod_autorizacao"   ,$obTAutorizacaoNotas->getDado("cod_autorizacao") );
            $obTAutorizacaoDocumento->setDado( "cod_documento"     ,$_REQUEST['stCodDocumento']                      );
            $obTAutorizacaoDocumento->setDado( "cod_tipo_documento",$_REQUEST['inCodTipoDocumento']                  );
            $obTAutorizacaoDocumento->inclusao();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgForm , $inNumAutorizacao , "incluir","aviso", Sessao::getId(), "../" );

        $arParametros = array(
            "stSerie" => $_REQUEST['stSerie'],
            "inQuantidadeTaloes" => $_REQUEST['inQuantidadeTaloes'],
            "inQuantidadeVias" => $_REQUEST['inQuantidadeVias'],
            "stObservacoes" => $_REQUEST['stObservacoes'],
            "inNotaFiscalInicial" => $_REQUEST['inNotaFiscalInicial'],
            "inNotaFiscalFinal" => $_REQUEST['inNotaFiscalFinal'],
            "inInscricaoEconomica" => $_REQUEST['inInscricaoEconomica'],
            "inCGM" => $_REQUEST['inCGM'],
            "stCodDocumento" => $_REQUEST['stCodDocumento'],
            "inCodTipoDocumento" => $_REQUEST['inCodTipoDocumento'],
            "inNumAutorizacao" => $inNumAutorizacao
        );

        $obVisao->emitirAutorizacao( $arParametros );
        break;
}
