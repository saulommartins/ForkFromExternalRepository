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
/*
* $Id: IniciaSessao.php 59613 2014-09-02 12:08:34Z gelson $
*/
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';

    $stUsuario  = "internet";
    $stSenha    = "internet";
    $stExercicio= "2006";

    //Verifica se o usuário informado existe alterado este commnet
    $sessao = new Sessao;
    $sessao->setUsername( $stUsuario );
    $sessao->setPassword( $stSenha   );
    $sessao->numCgm     = 0 ;
    $sessao->exercicio = $stExercicio;

    $obConexao = new Conexao;
    $obErro = $obConexao->abreConexao();
    if ( !$obErro->ocorreu() ) {
        $sessao->validaSessao();
        $obErro = $sessao->consultarDadosSessao();
        if ( !$obErro->ocorreu() ) {
            $obErro = $sessao->verificarSistemaAtivo();
            if ( !$obErro->ocorreu() ) {
                $obErro = $sessao->buscarLinksMaisAcessados( $arLinksMaisAcessaodos );
            }
        }
    }
