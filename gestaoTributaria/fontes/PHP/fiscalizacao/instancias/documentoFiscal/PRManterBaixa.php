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
    * Página oculta do formulário de Baixa de Notas Fiscais

    * Data de Criação   : 01/08/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: PRManterBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GT_FIS_MAPEAMENTO."TFISBaixaAutorizacao.class.php");
include_once(CAM_GT_FIS_MAPEAMENTO."TFISBaixaNotas.class.php");
include_once(CAM_GT_FIS_VISAO."VFISEmitirBaixaDocumentoFiscal.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterBaixa";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
$numCgm = Sessao::read('numCgm');

switch ($_REQUEST['stAcao']) {

    case 'baixar':

        $obTBaixaAutorizacao = new TFISBaixaAutorizacao();
        $obTBaixaNotas       = new TFISBaixaNotas();

        Sessao::getTransacao()->setMapeamento( $obTBaixaAutorizacao );

        $obTBaixaAutorizacao->proximoCod( $inNumBaixa );

        $obTBaixaAutorizacao->setDado( "cod_baixa"      ,$inNumBaixa                  );
        $obTBaixaAutorizacao->setDado( "cod_autorizacao",$_REQUEST['cod_autorizacao'] );
        $obTBaixaAutorizacao->setDado( "numcgm_usuario" ,Sessao::read('numCgm')              );
        $obTBaixaAutorizacao->setDado( "observacao"     ,$_REQUEST['stObservacoes']   );

        $obTBaixaAutorizacao->inclusao();

        $arValores = Sessao::read('arValores');

        if (count($arValores)>0) {
            foreach ($arValores as $key) {
                $inNumTipo = explode( "-",$key['inutilizacao']);
                $inNumTipo = intval( $inNumTipo[0] );

                $obTBaixaNotas->setDado( "cod_baixa", $inNumBaixa     );
                $obTBaixaNotas->setDado( "nr_nota"  , $key['nr_nota'] );
                $obTBaixaNotas->setDado( "cod_tipo" , $inNumTipo      );
                $obTBaixaNotas->inclusao();
            }
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=baixar",$inNumBaixa,"incluir","aviso",Sessao::getId(),"../");
        } else {
            sistemaLegado::exibeAviso( "Deve existir ao menos um dado para a listagem de notas.","n_incluir","aviso" );
        }
        // Emitir BAIXA DE DOCUMENTOS FISCAIS
        $_REQUEST['cod_baixa'] = $inNumBaixa;
        $obVisao = new VFISEmitirBaixaDocumentoFiscal;
        $obVisao->emitirBaixaDocFiscal($_REQUEST);

        break;
}
Sessao::encerraExcecao();
?>
