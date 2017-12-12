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
    * Manutneção de impressoras
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    * Casos de uso: uc-01.03.92

    $Id: incluiImpressora.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_FW_LEGADO."impressorasLegado.class.php";
include_once CAM_FW_LEGADO."mascarasLegado.lib.php";

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";

setAjuda( "UC-01.03.92" );

$ctrl = $_REQUEST['ctrl'];

if (!(isset($ctrl)))
    $ctrl = 0;

$pgProc = 'incluiImpressora.php';

switch ($ctrl) {

    # Carrega o form para inclusão.
    case 0:

        $obForm = new Form;
        $obForm->setAction ($pgProc);
        #$obForm->setTarget ("oculto");

        $obFormulario = new Formulario;
        $obFormulario->addForm($obForm);

        $obHiddenCtrl = new Hidden;
        $obHiddenCtrl->setName('ctrl');
        $obHiddenCtrl->setId  ('ctrl');
        $obHiddenCtrl->setValue('1');

        $obTextNomeImpressora = new TextBox;
        $obTextNomeImpressora->setRotulo	('Nome da Impressora');
        $obTextNomeImpressora->setName		('nom_impressora');
        $obTextNomeImpressora->setId		('nom_impressora');
        $obTextNomeImpressora->setSize		(15);
        $obTextNomeImpressora->setMaxLength (30);
        $obTextNomeImpressora->setNull		(false);

        $obTextFilaImpressao = new TextBox;
        $obTextFilaImpressao->setRotulo('Fila de Impressão');
        $obTextFilaImpressao->setName  ('fila_impressao');
        $obTextFilaImpressao->setId	   ('fila_impressao');
        $obTextFilaImpressao->setNull  (false);
        $obTextFilaImpressao->setSize  (10);

        $obFormulario->addTitulo('Dados para impressora');
        $obFormulario->addHidden($obHiddenCtrl);
        $obFormulario->addComponente($obTextNomeImpressora);
        $obFormulario->addComponente($obTextFilaImpressao);

        $obIMontaOrganograma = new IMontaOrganograma;
        $obIMontaOrganograma->setNivelObrigatorio(1);
        $obIMontaOrganograma->geraFormulario($obFormulario);

        $obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
        $obIMontaOrganogramaLocal->setNull(false);
        $obIMontaOrganogramaLocal->geraFormulario($obFormulario);

        $obFormulario->Ok();
        $obFormulario->show();

    break;

    # Após o submit do Formulário, inicia o processamento.
    case 1:

        $nom_impressora  = $_REQUEST['nom_impressora'];
        $fila_impressao  = $_REQUEST['fila_impressao'];
        $codLocal        = $_REQUEST['inCodLocal'];
        $codImpressoraId = pegaID("cod_impressora", "administracao.impressora");

        # Conta o nro de níveis selecionados.
        $inUltimoNivel = count(explode('.', $_REQUEST['hdninCodOrganograma']));

        $arOrganograma = explode('§', $_REQUEST['inCodOrganograma_'.$inUltimoNivel]);
        $codOrgao      = $arOrganograma[1];

        $inclusao = new impressorasLegado;
        $inclusao->setaVariaveis($codImpressoraId, $nom_impressora, $fila_impressao, $codOrgao, $codLocal);

        if (comparaValor("nom_impressora", $nom_impressora, "administracao.impressora")) {
            if (comparaValor("fila_impressao", $fila_impressao, "administracao.impressora")) {
                if ($inclusao->insereImpresssora()) {
                    include CAM_FW_LEGADO."auditoriaLegada.class.php";
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nom_impressora);
                    $audicao->insereAuditoria();
                    SistemaLegado::alertaAviso($pgProc, $nom_impressora, 'incluir', 'aviso', Sessao::getId(), '');
                } else {
                    SistemaLegado::alertaAviso($pgProc, $nom_impressora, 'n_incluir', 'erro', Sessao::getId(), '');
                }
            } else {
                SistemaLegado::alertaAviso($pgProc, "Já existe uma impressora com a fila de impressão: $fila_impressao", 'n_incluir', 'erro', Sessao::getId(), '');
            }
        } else {
            SistemaLegado::alertaAviso($pgProc, "A impressora $nom_impressora já existe", 'n_incluir', 'erro', Sessao::getId(), '');
        }
    break;

    # Monster Kill.
    /*
    case 100:
        $variavel        = $_REQUEST['variavel'];
        $valor           = $_REQUEST['valor'];
        $controle        = $_REQUEST['controle'];
        $codOrgao        = $_REQUEST['codOrgao'];
        $codUnidade      = $_REQUEST['codUnidade'];
        $codDepartamento = $_REQUEST['codDepartamento'];
        $codSetor		 = $_REQUEST['codSetor'];
        $codLocal		 = $_REQUEST['codLocal'];

        include(CAM_FW_LEGADO."filtrosCALOLegado.inc.php");
    break;
    */
}

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
