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
    * Formulário de Replicação das Entidades
    * Data de Criação   : 04/06/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";

// Define o nome dos arquivos PHP
$stPrograma = "ReplicarEntidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case "replicar":
        $obROrcamentoEntidade = new ROrcamentoEntidade;
        $obROrcamentoEntidade->setExercicio($_REQUEST['stExercicio']);
        $obROrcamentoEntidade->listar($rsEntidadeReplicar);

        if ($rsEntidadeReplicar->getNumLinhas() < 1) {
            $obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
            $obROrcamentoEntidade->listar($rsEntidade);

            if ($rsEntidade->getNumLinhas() > -1) {
                while (!$rsEntidade->EOF()) {
                    $stExercicio      = $rsEntidade->getCampo('exercicio');
                    $inCodEntidade    = $rsEntidade->getCampo('cod_entidade');
                    $inNumCGM         = $rsEntidade->getCampo('numcgm');
                    $inCodResponsavel = $rsEntidade->getCampo('cod_responsavel');
                    $inCodRespTecnico = $rsEntidade->getCampo('cod_resp_tecnico');
                    $inCodProfissao   = $rsEntidade->getCampo('cod_profissao');

                    $obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
                    $obROrcamentoEntidade->setNumCGM($inNumCGM);
                    $obROrcamentoEntidade->setCodigoResponsavel($inCodResponsavel);
                    $obROrcamentoEntidade->setCodigoResponsavelTecnico($inCodRespTecnico);
                    $obROrcamentoEntidade->setCodigoProfissao($inCodProfissao);

                    // Busca os usuários permitidos do exercício logado para replicar para o exercício informado
                    $obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
                    $obROrcamentoEntidade->listarUsuariosPermitidos($rsUsuariosPermitidos);
                    $obROrcamentoEntidade->setUsuarios( "" );
                    while (!$rsUsuariosPermitidos->EOF()) {
                        $obROrcamentoEntidade->addUsuario();
                        $obROrcamentoEntidade->obUltimoUsuario->setNumCGM($rsUsuariosPermitidos->getCampo('numcgm') );
                        $obROrcamentoEntidade->commitUsuario();

                        $rsUsuariosPermitidos->proximo();
                    }

                    $obROrcamentoEntidade->setExercicio($_REQUEST['stExercicio']);
                    $obROrcamentoEntidade->incluir($boTransacao);

                    $rsEntidade->proximo();
                }
                SistemaLegado::alertaAviso($pgForm."?stAcao=".$stAcao,"Replicar Entidade para o exercício de ".$_REQUEST['stExercicio'],"incluir","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso('Já existe entidade para o exercício informado',"n_incluir","erro", Sessao::getId(), "../");
        }

    break;
}
?>
