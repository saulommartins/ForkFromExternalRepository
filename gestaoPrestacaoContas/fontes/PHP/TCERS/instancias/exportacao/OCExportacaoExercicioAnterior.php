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
    * Página Oculta - Exportação Arquivos Exercício Anterior
    * Data de Criação   : 04/05/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: OCExportacaoExercicioAnterior.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.08.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_EXPORTADOR );
include_once( CAM_GPC_TCERS_NEGOCIO."RExportacaoTCERSArquivosExercicioAnterior.class.php" );
include_once( CAM_GPC_TCERS_MAPEAMENTO."TExportacaoTCERSConfiguracao.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"  );

/**
*   Tratar data inicial e final
*/
$arFiltro = Sessao::read('filtroRelatorio');
$inPeriodo      = Sessao::read('inPeriodo');
$inTipoPeriodo  = Sessao::read('inTipoPeriodo');
$stExercicio = Sessao::getExercicio()-1;

$inTmsInicial   = mktime(0,0,0,01,01,$stExercicio   );
$stDataInicial  = date  ('d/m/Y',$inTmsInicial      );

$inTmsFinal     = mktime(0,0,0,12,31,$stExercicio   );
$stDataFinal    = date  ('d/m/Y',$inTmsFinal        );

//cabecalho
$inTmsInicialCab   = mktime(0,0,0,01,01,Sessao::getExercicio() )       ;
$stDataInicialCab  = date  ('d/m/Y',$inTmsInicialCab           )       ;

$inMes = ($inPeriodo * $inTipoPeriodo) + 1;
if ($inMes == 13) {
    $inTmsFinalCab  = mktime(0,0,0,12,31,Sessao::getExercicio() )       ;
    $stDataFinalCab = date  ('d/m/Y',$inTmsFinalCab             )       ;
} else {
    $inTmsFinalCab  = mktime(0,0,0,$inMes,01,Sessao::getExercicio()) -1;
    $stDataFinalCab = date  ('d/m/Y',$inTmsFinalCab) ;
}
$arFiltro['stDataInicio'] = str_replace('/','',$stDataInicialCab);
$arFiltro['stDataFinal'] = str_replace('/','',$stDataFinalCab);

$arFiltro = Sessao::write('exp_arFiltro',$arFiltro);

$setorGoverno = explode('|', $arFiltro['stCnpjSetor']);

$stAcao = $request->get('stAcao');

if ($arFiltro['arEntidadesSelecionadas']) {
    $stEntidades    = implode(",",$arFiltro['arEntidadesSelecionadas']);
}
$obRTcersArquivosExercicioAnterior = new RExportacaoTcersArquivosExercicioAnterior;
$obRTcersArquivosExercicioAnterior->setArquivos    ( $arFiltro["arArquivosSelecionados"] );
$obRTcersArquivosExercicioAnterior->setExercicio   ( $stExercicio                        );
$obRTcersArquivosExercicioAnterior->setDataInicial ( $stDataInicial                      );
$obRTcersArquivosExercicioAnterior->setDataFinal   ( $stDataFinal                        );
$obRTcersArquivosExercicioAnterior->setHost        ( $arFiltro["stHost"]                 );
$obRTcersArquivosExercicioAnterior->setPorta       ( $arFiltro["stPorta"]                );
$obRTcersArquivosExercicioAnterior->setBanco       ( $arFiltro["stBanco"]                );
$obRTcersArquivosExercicioAnterior->setUsuario     ( $arFiltro["stUsuario"]              );
$obRTcersArquivosExercicioAnterior->setPeriodo     (1);
$obRTcersArquivosExercicioAnterior->setTipoPeriodo (12);
$obRTcersArquivosExercicioAnterior->setCodEntidades( $stEntidades                        );
$obRTcersArquivosExercicioAnterior->geraRecordset  ( $arRecordSet                        );

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio( Sessao::getExercicio()    );
$obRConfiguracaoOrcamento->consultarConfiguracao();

$obTExportacaoConfiguracao = new TExportacaoTCERSConfiguracao;

$inCodPrefeitura = $obRConfiguracaoOrcamento->getCodEntidadePrefeitura();
$inCodCamara     = $obRConfiguracaoOrcamento->getCodEntidadeCamara();
$inCodRPPS       = $obRConfiguracaoOrcamento->getCodEntidadeRPPS();

$obTExportacaoConfiguracao->setDado("parametro","orgao_unidade_prefeitura");
$obTExportacaoConfiguracao->consultar();
$inCodOUExecutivo = $obTExportacaoConfiguracao->getDado("valor");

$obTExportacaoConfiguracao->setDado("parametro","orgao_unidade_camara");
$obTExportacaoConfiguracao->consultar();
$inCodOULegislativo = $obTExportacaoConfiguracao->getDado("valor");

$obTExportacaoConfiguracao->setDado("parametro","orgao_unidade_rpps");
$obTExportacaoConfiguracao->consultar();
$inCodOURPPS = $obTExportacaoConfiguracao->getDado("valor");

$obTExportacaoConfiguracao->setDado("parametro","orgao_unidade_outros");
$obTExportacaoConfiguracao->consultar();
$inCodOUOutros = $obTExportacaoConfiguracao->getDado("valor");

$obExportador = new Exportador();
if (in_array("BVMOVANT.TXT",$arFiltro["arArquivosSelecionados"])) {

  $arDadosArquivo = array();

  if ($arRecordSet["BVMOVANT.TXT"]->getNumLinhas() >= 1) {
        $arElementos = $arRecordSet["BVMOVANT.TXT"]->getElementos();
        $inCount = 0;
        foreach ($arElementos as $key => $array) {
              $arDadosArquivo[$inCount]['cod_conta'] = $array['cod_conta'];
              $arDadosArquivo[$inCount]['mov_deb_1'] = $array['mov_deb_1'];
              $arDadosArquivo[$inCount]['mov_cre_1'] = $array['mov_cre_1'];
              $arDadosArquivo[$inCount]['mov_deb_2'] = $array['mov_deb_2'];
              $arDadosArquivo[$inCount]['mov_cre_2'] = $array['mov_cre_2'];
              $arDadosArquivo[$inCount]['mov_deb_3'] = $array['mov_deb_3'];
              $arDadosArquivo[$inCount]['mov_cre_3'] = $array['mov_cre_3'];
              $arDadosArquivo[$inCount]['mov_deb_4'] = $array['mov_deb_4'];
              $arDadosArquivo[$inCount]['mov_cre_4'] = $array['mov_cre_4'];
              $arDadosArquivo[$inCount]['mov_deb_5'] = $array['mov_deb_5'];
              $arDadosArquivo[$inCount]['mov_cre_5'] = $array['mov_cre_5'];
              $arDadosArquivo[$inCount]['mov_deb_6'] = $array['mov_deb_6'];
              $arDadosArquivo[$inCount]['mov_cre_6'] = $array['mov_cre_6'];
              switch ($setorGoverno[0]) {
                case $inCodPrefeitura   : $arDadosArquivo[$inCount]['orgao_unidade'] = $inCodOUExecutivo; break;
                case $inCodCamara       : $arDadosArquivo[$inCount]['orgao_unidade'] = $inCodOULegislativo; break;
                case $inCodRPPS         : $arDadosArquivo[$inCount]['orgao_unidade'] = $inCodOURPPS; break;
                DEFAULT                 : $arDadosArquivo[$inCount]['orgao_unidade'] = $inCodOUOutros;
              }
              $inCount++;
        }
    }

    $arBVMOVANT = new RecordSet;
    $arBVMOVANT->preenche($arDadosArquivo);

    $obExportador->addArquivo("BVMOVANT.TXT");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arBVMOVANT);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_conta");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_deb_1");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_cre_1");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_deb_2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_cre_2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_deb_3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_cre_3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_deb_4");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_cre_4");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_deb_5");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_cre_5");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_deb_6");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mov_cre_6");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

}

if (in_array("BREC_ANT.TXT",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("BREC_ANT.TXT");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");

    $obExportador->roUltimoArquivo->addBloco($arRecordSet["BREC_ANT.TXT"]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

    switch ($setorGoverno[0]) {
        case $inCodPrefeitura   : $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodOUExecutivo); break;
        case $inCodCamara       : $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodOULegislativo); break;
        case $inCodRPPS         : $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodOURPPS); break;
        DEFAULT                 : $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodOUOutros);
    }

    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_original");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("totalizado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(170);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nivel");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    if (Sessao::getExercicio() > '2008') {

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_caracteristica");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
    }

}

if (in_array("REC_ANT.TXT",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("REC_ANT.TXT");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arRecordSet["REC_ANT.TXT"]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

    switch ($setorGoverno[0]) {
        case $inCodPrefeitura   : $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodOUExecutivo); break;
        case $inCodCamara       : $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodOULegislativo); break;
        case $inCodRPPS         : $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodOURPPS); break;
        DEFAULT                 : $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodOUOutros);
    }

    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

    for ($inCont=1;$inCont<=12;$inCont++) {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("receita_mes".$inCont);
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
    }

    if (Sessao::getExercicio() > '2008') {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_caracteristica");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
    }

}

if (in_array("BVER_ANT.TXT",$arFiltro["arArquivosSelecionados"])) {

 $arDadosArquivo = array();
 if ($arRecordSet["BVER_ANT.TXT"]->getNumLinhas() >= 1) {
        $arElementos = $arRecordSet["BVER_ANT.TXT"]->getElementos();
        $inCount = 0;
        foreach ($arElementos as $key => $array) {
              $arDadosArquivo[$inCount]['cod_estrutural']          = $array['cod_estrutural'];
              $arDadosArquivo[$inCount]['nom_sistema']             = $array['nom_sistema'];
              $arDadosArquivo[$inCount]['nivel']                   = $array['nivel'];
              $arDadosArquivo[$inCount]['nom_conta']               = $array['nom_conta'];
              $arDadosArquivo[$inCount]['saldo_anterior_devedora'] = $array['saldo_anterior_devedora'];
              $arDadosArquivo[$inCount]['saldo_anterior_credora']  = $array['saldo_anterior_credora'];
              $arDadosArquivo[$inCount]['vl_saldo_debitos']        = $array['vl_saldo_debitos'];
              $arDadosArquivo[$inCount]['vl_saldo_creditos']       = $array['vl_saldo_creditos'];
              $arDadosArquivo[$inCount]['saldo_atual_devedora']    = $array['saldo_atual_devedora'];
              $arDadosArquivo[$inCount]['saldo_atual_credora']     = $array['saldo_atual_credora'];
              $arDadosArquivo[$inCount]['tipo_conta']              = $array['tipo_conta'];
              $arDadosArquivo[$inCount]['branco']                  = '';
              $arDadosArquivo[$inCount]['escrituracao'] =               $array['escrituracao'];
              $arDadosArquivo[$inCount]['indicador_superavit'] =        $array['indicador_superavit'];

              switch ($setorGoverno[0]) {
                case $inCodPrefeitura   : $arDadosArquivo[$inCount]['orgao_unidade'] = $inCodOUExecutivo; break;
                case $inCodCamara       : $arDadosArquivo[$inCount]['orgao_unidade'] = $inCodOULegislativo; break;
                case $inCodRPPS         : $arDadosArquivo[$inCount]['orgao_unidade'] = $inCodOURPPS; break;
                DEFAULT                 : $arDadosArquivo[$inCount]['orgao_unidade'] = $inCodOUOutros;
              }
              $inCount++;
        }
    }

    $arBVER_ANT = new RecordSet;
    $arBVER_ANT->preenche($arDadosArquivo);

    $obExportador->addArquivo("BVER_ANT.TXT");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arBVER_ANT);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_anterior_devedora");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_anterior_credora");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_debitos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_creditos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_atual_devedora");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_atual_credora");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_conta");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(148);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nivel");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    if (Sessao::getExercicio() > '2014') {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("escrituracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_sistema");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_superavit");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

    } else {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_sistema");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
    }

}

if (in_array("BRUB_ANT.TXT",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("BRUB_ANT.TXT");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arRecordSet["BRUB_ANT.TXT"]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subprograma");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_pao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("empenhado_1");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("empenhado_2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("empenhado_3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("empenhado_4");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("empenhado_5");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("empenhado_6");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("liquidado_1");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("liquidado_2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("liquidado_3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("liquidado_4");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("liquidado_5");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("liquidado_6");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pago_1");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pago_2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pago_3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pago_4");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pago_5");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pago_6");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
}
if ($arFiltro['stTipoExport'] == 'compactados') {
    $obExportador->setNomeArquivoZip('ExportacaoArquivosExercicioAnterior.zip');
}

$obExportador->show();

?>
