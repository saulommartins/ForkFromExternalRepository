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

    * $Id: PRManterAutenticacao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISAutenticacaoLivro.class.php"                                    );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISAutenticacaoDocumento.class.php"                                );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutenticacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

switch ($_REQUEST['stAcao']) {
    case 'autenticar':
        $obTAutenticacaoLivro     = new TFISAutenticacaoLivro();
        $obTAutenticacaoDocumento = new TFISAutenticacaoDocumento();
        $rsAutenticacaoLivro      = new RecordSet();

        Sessao::getTransacao()->setMapeamento( $obTAutenticacaoLivro );

        $stFiltro = " WHERE autenticacao_livro.inscricao_economica = ".$_REQUEST['inInscricaoEconomica']." \n";
        $stFiltro.= "   AND autenticacao_livro.nr_livro            = ".$_REQUEST['inNumeroLivro']."        \n";

        $obTAutenticacaoLivro->recuperaTodos( $rsAutenticacaoLivro,$stFiltro );
        if ( $rsAutenticacaoLivro->Eof() ) {
            $obTAutenticacaoLivro->setDado( "inscricao_economica", $_REQUEST['inInscricaoEconomica'] );
            $obTAutenticacaoLivro->setDado( "nr_livro"           , $_REQUEST['inNumeroLivro']        );
            $obTAutenticacaoLivro->setDado( "periodo_inicio"     , $_REQUEST['stDataInicial']        );
            $obTAutenticacaoLivro->setDado( "periodo_termino"    , $_REQUEST['stDataFinal']          );
            $obTAutenticacaoLivro->setDado( "qtd_paginas"        , $_REQUEST['inQuantidadePaginas']  );
            $obTAutenticacaoLivro->setDado( "observacao"         , $_REQUEST['stObservacoes']        );

            $obTAutenticacaoLivro->inclusao();

            $obTAutenticacaoDocumento->setDado( "inscricao_economica", $_REQUEST['inInscricaoEconomica'] );
            $obTAutenticacaoDocumento->setDado( "nr_livro"           , $_REQUEST['inNumeroLivro']        );
            $obTAutenticacaoDocumento->setDado( "cod_tipo_documento" , $_REQUEST['inCodTipoDocumento']   );
            $obTAutenticacaoDocumento->setDado( "cod_documento"      , $_REQUEST['stCodDocumento']       );

            $obTAutenticacaoDocumento->inclusao();
            sistemaLegado::alertaAviso($pgForm,$_REQUEST["inNumeroLivro"],"incluir","aviso",Sessao::getId(),"../");
        } else {
            sistemaLegado::exibeAviso("Livro já cadastrado para essa inscrição.","n_incluir","erro");
        }
        break;
}
Sessao::encerraExcecao();
