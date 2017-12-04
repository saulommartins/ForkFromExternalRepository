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
    * Página de Processamento de Incluir Notas Explicativas
    * Data de Criação   : 03/09/2007

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo S. Rodrigues

    * @ignore

    * $Id: PRManterNotasExplicativas.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.34
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_CONT_NEGOCIO."RContabilidadeNotasExplicativas.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotasExplicativas";

$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJs      = "JS".$stPrograma.".js";

$obRContabilidadeNotasExplicativas  = new RContabilidadeNotasExplicativas;

$stAcao = $request->get('stAcao');
$arValores = Sessao::read('arValores');

switch ($stAcao) {

    case 'incluir':

        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeNotasExplicativas.class.php" );
        $obTContabilidadeNotaExplicativa = new TContabilidadeNotasExplicativas;

        $rsRecordSetItem  = new RecordSet;
        $rsInclusos       = new RecordSet;

        $obTContabilidadeNotaExplicativa->recuperaNotaExplicativa($rsInclusos);

        $obTContabilidadeNotaExplicativa->recuperaNotaExplicativa($rsRecordSetItem);
        while ( !$rsRecordSetItem->eof() ) {
            $obTContabilidadeNotaExplicativa->setDado("nota_explicativa", $rsRecordSetItem->getCampo('nota_explicativa') );
            $obTContabilidadeNotaExplicativa->setDado("cod_acao"		, $rsRecordSetItem->getCampo('cod_acao') );
            $obTContabilidadeNotaExplicativa->setDado("dt_inicial"      , $rsRecordSetItem->getCampo('dt_inicial') );
            $obTContabilidadeNotaExplicativa->setDado("dt_final"        , $rsRecordSetItem->getCampo('dt_final') );
            $obTContabilidadeNotaExplicativa->exclusao();

            $rsRecordSetItem->proximo();
        }

        $inCountValores = count($arValores);
        for ($inPosTransf = 0; $inPosTransf < $inCountValores; $inPosTransf++) {
            $stNotaExplicativa = $arValores[$inPosTransf]['stNotaExplicativa'];
            $obTContabilidadeNotaExplicativa->setDado('cod_acao'        , $arValores[$inPosTransf]['inCodAcao']);
            $obTContabilidadeNotaExplicativa->setDado('nota_explicativa', $stNotaExplicativa);
            $obTContabilidadeNotaExplicativa->setDado('dt_inicial'      , $arValores[$inPosTransf]['stDtInicial']);
            $obTContabilidadeNotaExplicativa->setDado('dt_final'        , $arValores[$inPosTransf]['stDtFinal']);
            $obTContabilidadeNotaExplicativa->inclusao();
        }

        SistemaLegado::alertaAviso($pgForm,"Lista atualizada","incluir","aviso", Sessao::getId(), "../");

    break;

}
?>
