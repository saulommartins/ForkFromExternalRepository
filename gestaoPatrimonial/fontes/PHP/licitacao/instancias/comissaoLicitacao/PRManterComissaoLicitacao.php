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
    * Pagina de formulário para Cadastro de Comissão de licitação
    * Data de Criação   : 28/08/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * $Id: PRManterComissaoLicitacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.09
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoComissaoLicitacao.class.php"                           );
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoComissaoMembros.class.php"                             );
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoMembroExcluido.class.php"                              );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterComissaoLicitacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao"; ;
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

if ($stAcao != "excluir") {
    $stMensagem = validaVigenciaComissao();
}

if ($stMensagem == "") {
    $arMembros = Sessao::read('arMembros');
    $arMembrosExcluidos = Sessao::read('arMembrosExcluidos');

    switch ($stAcao) {
    case 'incluir':

        /// Validações
        $obErro = new Erro;
        foreach ($arMembros as $chave=> $dados) {
            if ($dados['tipo'] == 'Presidente') {
                $boPresidente = true;
            }
            if ($dados['tipo'] == 'Pregoeiro') {
                $boPregoeiro = true;
            }
        }

        if ( count($arMembros) == 0) {
            $obErro->setDescricao ( 'Escolha ao menos um membro para a comissão.' );
        } else {
            if ($_REQUEST['stFinalidade'] == 1 AND !$boPresidente) {
                $obErro->setDescricao('Escolha um presidente para a comissão.');
            }
            if ($_REQUEST['stFinalidade'] == 2 AND (!$boPregoeiro AND !$boPresidente)) {
                $obErro->setDescricao('Escolha um presidente ou pregoeiro.');
            }
            if ($_REQUEST['stFinalidade'] == 3 AND !$boPregoeiro) {
                    $obErro->setDescricao('Escolha um pregoeiro.');
            }
        }

        if ( !$obErro->ocorreu() ) {
            $inContPresidente = 0;
            $inContPregoeiro  = 0;
            foreach ($arMembros as $registro) {
                if ($registro['intipo'] == '2') {
                        $inContPresidente++;
                }
                if ($registro['intipo'] == '3') {
                        $inContPregoeiro++;
                }
            }

            if ($inContPresidente > 1)
                $obErro->setDescricao( 'Cadastre apenas um presidente para a comissão.' );

            if ($inContPregoeiro > 1)
                $obErro->setDescricao( ' Cadastre apenas um pregoeiro para a comissão.' );
        }

        if ( !$obErro->ocorreu() ) {

            include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoComissao.class.php' );
            ///incluindo a comissão
            $obTComissao = new TLicitacaoComissao;

            Sessao::setTrataExcecao(true);
            Sessao::getTransacao()->setMapeamento( $obTComissao );

            $obTComissao->proximoCod( $inCodComissao );
            $obTComissao->setDado( 'cod_comissao'     , $inCodComissao         );
            $obTComissao->setDado( 'cod_tipo_comissao', $_POST['stFinalidade'] );
            $obTComissao->setDado( 'cod_norma'        , $_POST['inCodNorma']   );
            $obTComissao->setDado( 'ativo'            , true                   );
            $obErro = $obTComissao->inclusao();

            ///// incluindo os membros da comissão

            if ( !$obErro->ocorreu() ) {
                include_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoComissaoMembros.class.php' );
                $obTMembros = new TLicitacaoComissaoMembros;

                $obTMembros->obTLicitacaoComissao = $obTComissao;
                foreach ($arMembros as $registro) {
                    $obTMembros->setDado( 'cod_tipo_membro' , $registro['intipo']           );
                    $obTMembros->setDado( 'cod_norma'       , $registro['inCodNormaMembro'] );
                    $obTMembros->setDado( 'numcgm'          , $registro['numcgm']           );
                    $obTMembros->setDado( 'cargo'           , $registro['stCargoMembro']    );
                    $obTMembros->setDado( 'natureza_cargo'  , $registro['inNaturezaCargo']  );

                    $obErro = $obTMembros->inclusao();
                    if ( $obErro->ocorreu() ) {
                            break;
                    }
                }
            }

            Sessao::encerraExcecao();
        }
        if ( $obErro->ocorreu() ) {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        } else {
                sistemaLegado::alertaAviso($pgForm ,"Comissão ".$inCodComissao, "incluir","aviso", Sessao::getId(), "../");
        }
        break;

    case 'excluir':
        /// pegando a data da vigencia
        include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php");
        include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoComissao.class.php' );

        $obNormaDataTermino = new TNormaDataTermino;
        $obTComissao = new TLicitacaoComissao;

        $obNormaDataTermino->setDado('cod_norma',  $_GET['cod_norma'] );
        $obNormaDataTermino->consultar();
        $dtTermino = $obNormaDataTermino->getDado ( 'dt_termino' );

        $obTComissao->setDado( 'cod_comissao', $_GET['cod_comissao'] );
        $obTComissao->consultar();
        $boAtivo = ( $obTComissao->getDado( 'ativo' ) == 't' ) ? true : false;

        $obErro = new Erro;

        if ($dtTermino && !$boAtivo) {
                if (SistemaLegado::comparaDatas( date( 'd/m/Y' ), $dtTermino )) {
                        $obErro->setDescricao ( 'Esta comissão não pode ser ativada porque sua vigência já expirou.' );
                }
        }

        if (!$obErro->ocorreu()) {
                if ($boAtivo) {
                        $obTComissao->setDado( 'ativo', false );
                        $stMensagem = 'Comissão inativada.';
                } else {
                        $obTComissao->setDado( 'ativo', true );
                        $stMensagem = 'Comissão ativada.';
                }
                $obErro = $obTComissao->alteracao();
        }

        if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir",$stMensagem,"excluir","aviso", Sessao::getId(), "../");
        } else {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir", $obErro->getDescricao() ,"","erro", Sessao::getId(), "../");
        }
        break;

    case 'alterar':
        $obErro = new Erro;
        Sessao::setTrataExcecao(true);

        foreach ($arMembros as $chave=> $dados) {
            if ($dados['tipo'] == 'Presidente') {
                $boPresidente = true;
            }
            if ($dados['tipo'] == 'Pregoeiro') {
                $boPregoeiro = true;
            }
        }

        if ( count($arMembros) == 0) {
            $obErro->setDescricao ( 'Escolha ao menos um membro para a comissão.' );
        } else {
            if ($_REQUEST['inCodTipoComissao'] == 1 AND !$boPresidente) {
                $obErro->setDescricao('Escolha um presidente para a comissão.');
            }
            if ($_REQUEST['inCodTipoComissao'] == 2 AND (!$boPregoeiro AND !$boPresidente)) {
                $obErro->setDescricao('Escolha um presidente ou pregoeiro para a comissão.');
            }
            if ($_REQUEST['inCodTipoComissao'] == 3 AND !$boPregoeiro) {
                    $obErro->setDescricao('Escolha um pregoeiro para a comissão.');
            }
        }

        if (!$obErro->ocorreu()) {
            //Inclui todos os membros provenientes da lista
            $obTLicitacaoComissaoMembros = new TLicitacaoComissaoMembros;
            foreach ($arMembros as $registro) {
                $obTLicitacaoComissaoMembros->setDado( 'cod_comissao'    , $_REQUEST['stCodigoComissao']  );
                $obTLicitacaoComissaoMembros->setDado( 'numcgm'          , $registro['numcgm']            );
                $obTLicitacaoComissaoMembros->setDado( 'cod_norma'       ,  $_REQUEST['inCodNorma']       );
                
                $obTLicitacaoComissaoMembros->recuperaPorChave($rsMembrosComissao);
                if ($rsMembrosComissao->getNumLinhas() > 0) {
                    $obTLicitacaoComissaoMembros->setDado( 'cod_tipo_membro' , $registro['intipo']);
                    $obTLicitacaoComissaoMembros->setDado( 'cargo'           , $registro['stCargoMembro']    );
                    $obTLicitacaoComissaoMembros->setDado( 'natureza_cargo'  , $registro['inNaturezaCargo']  );
                    $obTLicitacaoComissaoMembros->alteracao();
                } else {
                    $obTLicitacaoComissaoMembros->setDado( 'cod_tipo_membro' , $registro['intipo']);
                    $obTLicitacaoComissaoMembros->setDado( 'cargo'           , $registro['stCargoMembro']    );
                    $obTLicitacaoComissaoMembros->setDado( 'natureza_cargo'  , $registro['inNaturezaCargo']  );
                    $obTLicitacaoComissaoMembros->inclusao();
                }
            }
            $obTLicitacaoComissaoMembros = new TLicitacaoComissaoMembros;
            $obTLicitacaoMembroExcluido = new TLicitacaoMembroExcluido;
            foreach ($arMembrosExcluidos as $registro) {
                $obTLicitacaoComissaoMembros->setDado('cod_norma'    , $registro['inCodNormaMembro']);
                $obTLicitacaoComissaoMembros->setDado('numcgm'       , $registro['numcgm']);
                $obTLicitacaoComissaoMembros->setDado('cod_comissao' , $registro['cod_comissao']);
                $obTLicitacaoComissaoMembros->recuperaPorChave($rsComissaoMembros);
                if ($rsComissaoMembros->getNumLinhas() > 0) {
                   $obTLicitacaoMembroExcluido->setDado('cod_norma'    , $registro['inCodNormaMembro']);
                   $obTLicitacaoMembroExcluido->setDado('numcgm'       , $registro['numcgm']);
                   $obTLicitacaoMembroExcluido->setDado('cod_comissao' , $registro['cod_comissao']);
                   $obTLicitacaoMembroExcluido->inclusao();
                }
            }
        }
        Sessao::encerraExcecao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Comissão alterada.","alterar","aviso", Sessao::getId(), "../");
        } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;
    }

} else {
    SistemaLegado::exibeAviso( $stMensagem, "n_incluir", "erro" );
}

function validaVigenciaComissao()
{
    $mensagemErroVigenciaValida = "";
    $obLicitacaoComissao   = new TLicitacaoComissaoLicitacao;
    $rsComissaoVigencia    = new Recordset;

    $inCodNorma = (isset($_REQUEST['inCodNorma']) ? $_REQUEST['inCodNorma'] : "");

    if (!empty($inCodNorma)) {
        $stFiltro = " WHERE norma.cod_norma = ".$inCodNorma;
        $obLicitacaoComissao->recuperaValidaVigenciaComissao($rsComissaoVigencia,$stFiltro);

        $arDataPublicacao = explode('/',$rsComissaoVigencia->getCampo('dt_publicacao'));
        $stDataPublicacao = $arDataPublicacao[2].$arDataPublicacao[1].$arDataPublicacao[0];

        $arDataHoje = date('Ymd');

        $arDataTermino = explode('/',$rsComissaoVigencia->getCampo('dt_termino'));
        $stDataTermino = $arDataTermino[2].$arDataTermino[1].$arDataTermino[0];

        if ( ($stDataPublicacao <= $arDataHoje) && ($stDataTermino >= $arDataHoje ) && ($rsComissaoVigencia->getCampo('dt_termino') != "") ) {
            return;
        } else {
            if (($rsComissaoVigencia->getCampo('dt_termino') == "")) {
                $mensagemErroVigenciaValida = "Efetue a alteração da norma (Código:".$inCodNorma." - ".$_REQUEST['hdStAtoDesignacaoMembro']."), para informar a data de término!";
            } else {
                $mensagemErroVigenciaValida = "A norma (Código:".$inCodNorma." - ".$_REQUEST['hdStAtoDesignacaoMembro'].") expirou, utilize outra norma!";
            }
        }
    }

    return $mensagemErroVigenciaValida;
}

?>
