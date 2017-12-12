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

    $Id: alteraImpressora.php 66029 2016-07-08 20:55:48Z carlos.silva $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_FW_LEGADO."impressorasLegado.class.php";
include_once CAM_FW_LEGADO."mascarasLegado.lib.php";
include_once CAM_FW_LEGADO."paginacaoLegada.class.php";
include_once CAM_FW_LEGADO."configuracaoLegado.class.php";
include_once CAM_FW_LEGADO."auditoriaLegada.class.php";

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";

setAjuda( "UC-01.03.92" );

$stMascaraLocal = pegaConfiguracao("mascara_local");

$ctrl   = $_REQUEST['ctrl'];
$pagina = $_REQUEST['pagina'];
$acao   = $_REQUEST['acao'];
$stAcao = $request->get('stAcao');

$pgProc = 'alteraImpressora.php';
?>
<script type="text/javascript">
    function zebra(id, classe)
    {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) == 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>
<?php
if (!(isset($ctrl)))
    $ctrl = 0;

if(!isset($pagina))
    $pagina = 0;

switch ($ctrl) {
    # Monta a tabela de Pesquisa de impressoras cadastradas, para edição.
    case 0:

        echo "<table width=100% id='impressoras'>";

        if (isset($acao)) {
            $stSQL  = "      SELECT                                                   \n";
            $stSQL .= "              impressora.cod_impressora                        \n";
            $stSQL .= "           ,  impressora.nom_impressora                        \n";
            $stSQL .= "           ,  impressora.fila_impressao                        \n";
            $stSQL .= "           ,  impressora.cod_orgao                             \n";
            $stSQL .= "           ,  impressora.cod_local                             \n";
            $stSQL .= "           ,  local.descricao as nom_local                     \n";
            $stSQL .= "           ,  orgao_descricao.descricao as nom_orgao           \n";
            $stSQL .= "                                                               \n";
            $stSQL .= "        FROM  administracao.impressora                         \n";
            $stSQL .= "                                                               \n";
            $stSQL .= "  INNER JOIN  organograma.orgao_descricao                      \n";
            $stSQL .= "          ON  orgao_descricao.cod_orgao = impressora.cod_orgao \n";
            $stSQL .= "                                                               \n";
            $stSQL .= "  INNER JOIN  organograma.local                                \n";
            $stSQL .= "          ON  local.cod_local = impressora.cod_local           \n";
            $stSQL .= "                                                               \n";
            $stSQL .= "       WHERE  impressora.cod_impressora > 0                    \n";

            Sessao::write('sSQL',$stSQL);
        }

        $sSQL = Sessao::read('sSQL');

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sSQL,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("impressora.nom_impressora","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $lista="";
        echo "<tr>
        <tr>
            <td class='alt_dados' colspan='9'>Dados para impressora</td>
        </tr>
            <td class='labelleftcabecalho' width='5%'>&nbsp;</td>
            <td class=labelleftcabecalho>Órgão</td>
            <td class=labelleftcabecalho>Local</td>
            <td class=labelleftcabecalho>Nome da Impressora</td>
            <td class=labelleftcabecalho>Fila de Impressão</td>
            <td class=labelleftcabecalho>Editar</td>
        </tr>";

        $count = $paginacao->contador();

        while (!$dbEmp->eof()) {
            $nom_impressora  = trim($dbEmp->pegaCampo("nom_impressora"));
            $cod_impressora = trim($dbEmp->pegaCampo("cod_impressora"));
            $filaImpressao = $dbEmp->pegaCampo("fila_impressao");
            $cod_orgao = trim($dbEmp->pegaCampo("cod_orgao"));
            $cod_local = trim($dbEmp->pegaCampo("cod_local"));
            $orgao = trim($dbEmp->pegaCampo("nom_orgao"));
            $local = trim($dbEmp->pegaCampo("nom_local"));
            $dbEmp->vaiProximo();
            $lista .= "<tr>
                 <td class='show_dados_center_bold'>".$count++."</td>
                 <td class=show_dados>".$orgao."</td>
                 <td class=show_dados>".$local."</td>
                 <td class=show_dados>".$nom_impressora."</td>
                 <td class=show_dados>".$filaImpressao."</td>
                 <td class=show_dados>
                 <a href=alteraImpressora.php?".Sessao::getId().
                    "&cod_impressora=".$cod_impressora.
                    "&cod_local=".$cod_local.
                    "&cod_orgao=".$cod_orgao.
                    "&ctrl=1".
                    "&pagina=".$pagina.
                    "&nom_impressora=".urlencode($nom_impressora).
                    "&fila_impressao=".urlencode($filaImpressao).">
                    <img src=".CAM_FW_IMAGENS."btneditar.gif border=0>
                 </a>
                 </td>
               </tr>";
        }

        echo $lista;
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
            $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
        echo "</table>";
?>
<script>zebra('impressoras','zb');</script>
<?php
    break;

    # Prepara o formulário para edição.
    case 1:

        $cod_impressora = $_REQUEST['cod_impressora'];
        $nom_impressora = $_REQUEST['nom_impressora'];
        $fila_impressao = $_REQUEST['fila_impressao'];
        $cod_local		= $_REQUEST['cod_local'];
        $cod_orgao		= $_REQUEST['cod_orgao'];
        $pagina 	    = $_REQUEST['pagina'];
        $ctrl 		    = $_REQUEST['ctrl'];

        $obForm = new Form;
        $obForm->setAction ($pgProc);

        $obFormulario = new Formulario;
        $obFormulario->addForm($obForm);

        $obHiddenCtrl = new Hidden;
        $obHiddenCtrl->setName('ctrl');
        $obHiddenCtrl->setId  ('ctrl');
        $obHiddenCtrl->setValue('2');

        $obHiddenLocal = new Hidden;
        $obHiddenLocal->setName ('cod_local');
        $obHiddenLocal->setId   ('cod_local');
        $obHiddenLocal->setValue($cod_local);

        $obHiddenOrgao = new Hidden;
        $obHiddenOrgao->setName ('cod_orgao');
        $obHiddenOrgao->setId   ('cod_orgao');
        $obHiddenOrgao->setValue($cod_orgao);

        $obHiddenCodImpressora = new Hidden;
        $obHiddenCodImpressora->setName ('cod_impressora');
        $obHiddenCodImpressora->setId   ('cod_impressora');
        $obHiddenCodImpressora->setValue($cod_impressora);

        $obTextNomeImpressora = new TextBox;
        $obTextNomeImpressora->setRotulo	('Nome da Impressora');
        $obTextNomeImpressora->setName		('nom_impressora');
        $obTextNomeImpressora->setId		('nom_impressora');
        $obTextNomeImpressora->setSize		(15);
        $obTextNomeImpressora->setMaxLength (30);
        $obTextNomeImpressora->setNull		(false);
        $obTextNomeImpressora->setValue		($nom_impressora);

        $obTextFilaImpressao = new TextBox;
        $obTextFilaImpressao->setRotulo('Fila de Impressão');
        $obTextFilaImpressao->setName  ('fila_impressao');
        $obTextFilaImpressao->setId	   ('fila_impressao');
        $obTextFilaImpressao->setNull  (false);
        $obTextFilaImpressao->setSize  (10);
        $obTextFilaImpressao->setValue ($fila_impressao);

        $obFormulario->addTitulo('Dados para impressora');
        $obFormulario->addHidden($obHiddenCtrl);
        $obFormulario->addHidden($obHiddenLocal);
        $obFormulario->addHidden($obHiddenOrgao);
        $obFormulario->addHidden($obHiddenCodImpressora);
        $obFormulario->addComponente($obTextNomeImpressora);
        $obFormulario->addComponente($obTextFilaImpressao);

        $obIMontaOrganograma = new IMontaOrganograma;
        $obIMontaOrganograma->setNivelObrigatorio(1);
        $obIMontaOrganograma->setCodOrgao($cod_orgao);
        $obIMontaOrganograma->geraFormulario($obFormulario);

        $obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
        $obIMontaOrganogramaLocal->setNull(false);
        $obIMontaOrganogramaLocal->setValue($cod_local);
        $obIMontaOrganogramaLocal->geraFormulario($obFormulario);

        $obFormulario->Ok();
        $obFormulario->show();

    break;

    # Prepara o processamento para edição.
    case 2:

        $ctrl           = $_REQUEST['ctrl'];
        $pagina         = $_REQUEST['pagina'];
        $cod_impressora = $_REQUEST['cod_impressora'];
        $nom_impressora = $_REQUEST['nom_impressora'];
        $fila_impressao = $_REQUEST['fila_impressao'];
        $codLocal       = $_REQUEST['inCodLocal'];

        # Conta o nro de níveis selecionados.
        $inUltimoNivel = count(explode('.', $_REQUEST['hdninCodOrganograma']));

        $arOrganograma = explode('§', $_REQUEST['inCodOrganograma_'.$inUltimoNivel]);
        $codOrgao      = $arOrganograma[1];

        $inclusao = new impressorasLegado;
        $inclusao->setaVariaveis($cod_impressora, $nom_impressora, $fila_impressao, $codOrgao, $codLocal);

        if (comparaValor("nom_impressora", $nom_impressora, "administracao.impressora", "and cod_impressora != $cod_impressora")) {
            if (comparaValor("fila_impressao", $fila_impressao, "administracao.impressora", "and cod_impressora != $cod_impressora")) {
                if ($inclusao->updateImpresssora()) {
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nom_impressora);
                    $audicao->insereAuditoria();
                    SistemaLegado::alertaAviso($pgProc, 'Impressora: '.$nom_impressora, 'alterar', 'aviso', Sessao::getId(), '');
                } else {
                    SistemaLegado::alertaAviso($pgProc, 'Impressora: '.$nom_impressora, 'n_alterar', 'erro', Sessao::getId(), '');
                }
            } else {
                SistemaLegado::alertaAviso($pgProc, "Já existe uma impressora com a fila de impressão: $fila_impressao", 'n_incluir', 'erro', Sessao::getId(), '');
            }
        } else {
            SistemaLegado::alertaAviso($pgProc, "A impressora $nom_impressora já existe", 'n_incluir', 'erro', Sessao::getId(), '');
        }

   break;

    case 100:

        $variavel = $_REQUEST['variavel'];
        $valor    = $_REQUEST['valor'];
        $controle = $_REQUEST['controle'];
        $codOrgao = $_REQUEST['codOrgao'];
        $codLocal = $_REQUEST['codLocal'];

        # $codUnidade = $_REQUEST['codUnidade'];
        # $codDepartamento = $_REQUEST['codDepartamento'];
        # $codSetor = $_REQUEST['codSetor'];

        include(CAM_FW_LEGADO."filtrosCALOLegado.inc.php");

    break;

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
