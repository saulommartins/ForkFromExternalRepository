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
    * Página Oculta - Exportação Arquivos GF

    * Data de Criação   : 18/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: $

    * Casos de uso: uc-06.06.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTRNConfiguracaoEntidade.class.php" );
include_once( CLA_EXPORTADOR );

SistemaLegado::BloqueiaFrames();

$stAcao = $request->get('stAcao');

$arFiltro = Sessao::read('filtroRelatorio');
$inBimestre     = $arFiltro['bimestre'];
$inCodEntidade  = implode(',',$arFiltro['inCodEntidade']);

$inPeriodo      = $inBimestre;

$inTmsInicial   = mktime(0,0,0,01,01,Sessao::getExercicio() )       ;
$stDataInicial  = date  ('d/m/Y',$inTmsInicial          )       ;

switch ($inPeriodo) {
    case 1:
        $inTmsInicial     = mktime(0,0,0,01,01,Sessao::getExercicio() )   ;
        $stDataInicial    = date  ('d/m/Y',$inTmsInicial            )     ;

        $inTmsFinal     = mktime(0,0,0,3,01,Sessao::getExercicio() ) - 1   ;
        $stDataFinal    = date  ('d/m/Y',$inTmsFinal            )       ;
        break;
    case 2:
        $inTmsInicial     = mktime(0,0,0,03,01,Sessao::getExercicio() )   ;
        $stDataInicial    = date  ('d/m/Y',$inTmsInicial            )     ;

        $inTmsFinal     = mktime(0,0,0,5,01,Sessao::getExercicio() ) - 1   ;
        $stDataFinal    = date  ('d/m/Y',$inTmsFinal            )       ;
        break;
    case 3:
        $inTmsInicial     = mktime(0,0,0,05,01,Sessao::getExercicio() )   ;
        $stDataInicial    = date  ('d/m/Y',$inTmsInicial            )     ;

        $inTmsFinal     = mktime(0,0,0,7,01,Sessao::getExercicio() ) - 1   ;
        $stDataFinal    = date  ('d/m/Y',$inTmsFinal            )       ;
        break;
    case 4:
        $inTmsInicial     = mktime(0,0,0,07,01,Sessao::getExercicio() )   ;
        $stDataInicial    = date  ('d/m/Y',$inTmsInicial            )     ;

        $inTmsFinal     = mktime(0,0,0,9,01,Sessao::getExercicio() ) - 1   ;
        $stDataFinal    = date  ('d/m/Y',$inTmsFinal            )       ;
        break;
    case 5:
        $inTmsInicial     = mktime(0,0,0,9,01,Sessao::getExercicio() )   ;
        $stDataInicial    = date  ('d/m/Y',$inTmsInicial            )     ;

        $inTmsFinal     = mktime(0,0,0,11,01,Sessao::getExercicio() ) - 1   ;
        $stDataFinal    = date  ('d/m/Y',$inTmsFinal            )       ;
        break;
    case 6:
        $inTmsInicial     = mktime(0,0,0,11,01,Sessao::getExercicio() )   ;
        $stDataInicial    = date  ('d/m/Y',$inTmsInicial            )     ;

        $inTmsFinal     = mktime(0,0,0,12,31,Sessao::getExercicio() )       ;
        $stDataFinal    = date  ('d/m/Y',$inTmsFinal            )       ;
        break;
}

$stTipoDocumento = "TCE_RN";
$obExportador    = new Exportador();

$obTMapeamento = new TOrcamentoEntidade();
$obTMapeamento->recuperaRelacionamento( $rsEntidade , " AND E.exercicio = '".Sessao::getExercicio()."' AND E.cod_entidade = ( SELECT valor::INTEGER
                                                                                                                            FROM administracao.configuracao
                                                                                                                           WHERE parametro = 'cod_entidade_prefeitura'
                                                                                                                             AND exercicio = '".Sessao::getExercicio()."' )");

$obTConfiguracao = new TTRNConfiguracaoEntidade();
// $obTConfiguracao->setDado('cod_entidade'  ,$inCodEntidade);
$obTConfiguracao->recuperaRelacionamento($rsConfiguracao);

$arFiltro['stCodOrgao'] = $rsConfiguracao->getCampo('valor');
$arFiltro['stNomeEntidade'] = $rsEntidade->getCampo('nom_cgm');

Sessao::write('exp_stNomeEntidade', $rsConfiguracao->getCampo('nom_cgm'));
Sessao::write('exp_stCodOrgao', $rsConfiguracao->getCampo('valor'));
Sessao::write('exp_bimestre', $inBimestre);

foreach ($arFiltro["arArquivosSelecionados"] as $stArquivo) {

        //$obExportador->addArquivo($stArquivo);-----------------------------------------original
        //$obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);-------------""

        $arArquivo = explode('.',$stArquivo);

        if (strstr($arArquivo[0], "BBAA") == "BBAA") {
            $stNome = str_replace("BBAA", ('0'.$inBimestre.substr(Sessao::getExercicio(),2,2).'.txt'),$arArquivo[0]);
            $obExportador->addArquivo($stNome);
        } else {
            $obExportador->addArquivo($stArquivo);
        }
        $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);

        $obExportador->roUltimoArquivo->addBloco( new RecordSet );
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        include( substr($stArquivo,0,strpos($stArquivo,'.TXT')) . ".inc.php");
        $arRecordSet = null;
}

if ($arFiltro['stTipoExport'] == 'compactados') {
    $obExportador->setNomeArquivoZip('ExportacaoArquivosPrincipais.zip');
}

Sessao::write('filtro', $arFiltro);

$obExportador->show();
SistemaLegado::LiberaFrames();

?>
