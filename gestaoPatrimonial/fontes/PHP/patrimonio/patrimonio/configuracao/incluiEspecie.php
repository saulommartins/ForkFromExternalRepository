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
    * Insere novas Especies no sistema de PATRIMÔNIO
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper

    * @ignore

    $Revision: 28299 $
    $Name$
    $Autor: $
    $Date: 2008-02-29 14:31:59 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-03.01.05
*/

/*
$Log$
Revision 1.23  2007/05/22 02:18:28  diego
Bug #9275#

Revision 1.22  2007/02/12 16:14:43  tonismar
bug #8329

Revision 1.21  2006/10/11 11:30:29  larocca
Bug #6909#

Revision 1.20  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.19  2006/07/13 20:23:19  fernando
Alteração de hints

Revision 1.18  2006/07/13 14:38:22  fernando
Alteração de hints

Revision 1.17  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.16  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.05");
$anoE = Sessao::getExercicio();
if (!(isset($ctrl))) {
    $ctrl = 0;
}
switch ($ctrl) {
    case 2:
       if ($codNatureza != $codNaturezaAnt) {
            $codGrupo = "";
        }

                $sSQL = "SELECT * FROM patrimonio.grupo WHERE cod_natureza = ".$codNatureza." ORDER by nom_grupo";

                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboGrupo = "";
                $i = 1;
                while (!$dbEmp->eof()) {
                    $codGrupof  = trim($dbEmp->pegaCampo("cod_grupo"));
                    $nomGrupo  = trim($dbEmp->pegaCampo("nom_grupo"));
                    $dbEmp->vaiProximo();
                    $comboGrupo .= "parent.window.frames['telaPrincipal'].document.frm.codGrupo.options[".$i."] = new Option ('".addslashes($nomGrupo)."',".$codGrupof.");";
                    if ($codGrupof == $codGrupo) {
                       $comboGrupo .= "parent.window.frames['telaPrincipal'].document.frm.codGrupo.selectedIndex = ".$i.";";
                    }
                    $i++;
                }

                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
                $comboGrupo .= "parent.window.frames['telaPrincipal'].document.frm.codNaturezaAnt.value = ".$codNatureza.";";
                $comboGrupo .= "parent.window.frames['telaPrincipal'].document.frm.codTxtGrupo.value = '".$codGrupo."';"
                ?>
                   <script> <?=$comboGrupo?></script>
                <?php

    break;
    //formulario para insercao da Especie com filtro por Natureza->Grupo
    case 0:
?>
    <script type="text/javascript">

        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;

            campo = document.frm.codGrupo.value;
                if (campo == "xxx") {
                mensagem += "@O campo Grupo é obrigatório.";
                erro = true;
            }

            campo = document.frm.codNatureza.value;
                if (campo == "xxx") {
                mensagem += "@O campo Natureza é obrigatório.";
                erro = true;
            }

            campo = trim(document.frm.nomEspecie.value).length;
                if (campo == 0) {
                mensagem += "@O campo Descrição da Espécie é obrigatório.";
                erro = true;
            }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);
        }

        function Salvar()
        {
            if (Valida()) {
                document.frm.action = "incluiEspecie.php?<?=Sessao::getId()?>&ctrl=1";
                document.frm.submit();
            }
        }

        // desabilita botao 'OK' se o valor informado no input text nao existir e vice-versa
        // submete o formulario para preencher os campos dependentes ao combo selecionado
        function verificaCombo(campo_a, campo_b)
        {
            var aux;
            aux = preencheCampo(campo_a, campo_b);
            var target = document.frm.target;
            if (aux == false) {
                document.frm.ok.disabled = true;
                document.frm.action = "incluiEspecie.php?<?=Sessao::getId()?>&ctrl=2";
                document.frm.target = 'oculto';
                document.frm.submit();
                document.frm.target = target;
            } else {
                document.frm.ok.disabled = false;
                document.frm.action = "incluiEspecie.php?<?=Sessao::getId()?>&ctrl=2";
                document.frm.target = 'oculto';
                document.frm.submit();
                document.frm.target = target;
            }
        }
   function frmReset()
   {
    document.frm.nomEspecie.value = "";
    document.frm.codNatureza.options.selectedIndex = -1;
    //verificaCombo(document.frm.codNatureza,document.frm.codTxtNatureza);
    document.frm.reset();
    document.frm.codTxtNatureza.focus();

    return(true);
   }
    </script>

    <form name="frm" action="incluiEspecie.php?<?=Sessao::getId()?>" method="POST" onreset="return frmReset();">

    <table width="100%">

    <tr>
        <td colspan="2" class="alt_dados">Dados da Espécie</td>
    </tr>

    <tr>
    <td class="label" width="20%" title="Selecione a natureza do bem.">*Natureza</td>
    <td class="field" width="80%">

        <input type="text" name="codTxtNatureza" value="<?=$codNatureza != "xxx" ? $codNatureza : "";?>" size="10" maxlength="10"
            onChange="javascript:verificaCombo(this, document.frm.codNatureza);"
            onKeyPress="return(isValido(this, event, '0123456789'));">
        <select name="codNatureza" onChange="javascript: verificaCombo(this, document.frm.codTxtNatureza); " style="width:200px">
            <option value="xxx">Selecione</option>
<?php
            $sSQL = "SELECT * FROM patrimonio.natureza ORDER by nom_natureza";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboNatureza = "";

            while (!$dbEmp->eof()) {
                $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
                $nomNatureza  = trim($dbEmp->pegaCampo("nom_natureza"));
                $dbEmp->vaiProximo();
                $comboNatureza .= "<option value=".$codNaturezaf;
                        if (isset($codNatureza)) {
                            if ($codNaturezaf == $codNatureza)
                            $comboNatureza .= " SELECTED";
                        }
                $comboNatureza .= ">".$nomNatureza."</option>\n";
            }

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

            echo $comboNatureza;
?>
        </select>
    </td>
    </tr>

<?php
        if ($codNatureza != $codNaturezaAnt) {
            $codGrupo = "";
        }
?>
        <input type="hidden" name="codNaturezaAnt" value="<?=$codNatureza;?>">

    <tr>
        <td class="label" title="Selecione o grupo do bem.">*Grupo</td>
        <td class="field">

        <input type="text" name="codTxtGrupo" size="10" maxlength="10" value="<?=$codGrupo != "xxx" ? $codGrupo : "";?>"
            onChange="javascript: verificaCombo(this, document.frm.codGrupo);"
            onKeyPress="return(isValido(this, event, '0123456789'));">

        <select name="codGrupo" onChange="javascript: verificaCombo(this, document.frm.codTxtGrupo);" style="width:200px">
            <option value="xxx" SELECTED>Selecione</option>
<?php
            if (isset($codNatureza)) {

                if ($codNatureza != "xxx") {

//              $sSQL = "SELECT * FROM patrimonio.grupo WHERE ano_exercicio = ".$anoE."
//                          AND cod_natureza = ".$codNatureza." ORDER by nom_grupo";

                    $sSQL = "SELECT * FROM patrimonio.grupo WHERE cod_natureza = ".$codNatureza." ORDER by nom_grupo";

                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboGrupo = "";

                while (!$dbEmp->eof()) {
                    $codGrupof  = trim($dbEmp->pegaCampo("cod_grupo"));
                    $nomGrupo  = trim($dbEmp->pegaCampo("nom_grupo"));
                    $dbEmp->vaiProximo();
                    $comboGrupo .= "<option value=".$codGrupof;
                            if (isset($codGrupo)) {
                                if ($codGrupof == $codGrupo)
                                $comboGrupo .= " SELECTED";
                            }
                    $comboGrupo .= ">".$nomGrupo."</option>\n";
                }

                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();

                echo $comboGrupo;
            }
        }
?>
        </select>

        </td>
    </tr>

    <tr>
        <td class="label" title="Informe a descrição da espécie do bem.">*Descrição da Espécie</td>
        <td class="field">
            <input type="text" name="nomEspecie" size="80" maxlength="80" value="<?=$nomEspecie;?>">
        </td>
    </tr>

<?php
    // se o Grupo estiver setado exibe listagem de atributos cadastrados para serem selecionados
//  if (isset($codGrupo)) {

//      if ($codGrupo != "xxx" and $codGrupo > 0 and $codNatureza != "xxx") {
?>
            <tr>
                <td class="label" title="Selecione os atributos.">Atributos</td>
                <td class="field">
<?php
                $sSQL = "SELECT * FROM administracao.atributo_dinamico ORDER by nom_atributo";
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $atributos = "";

                while (!$dbEmp->eof()) {
                    $cod_atributo  = trim($dbEmp->pegaCampo("cod_atributo"));
                    $nom_atributo  = trim($dbEmp->pegaCampo("nom_atributo"));
                    $dbEmp->vaiProximo();

                    $atributos.= "<input type=checkbox name='atributo[]' value=".$cod_atributo."> ".$nom_atributo."<br>";
                }

                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();

                echo $atributos;
?>
                </td>
            </tr>
<?php
//      }
//  }
?>

    <tr>
        <td class="field" colspan="2">
        <?php geraBotaoOk(); ?>
        </td>
    </tr>

    </table>

    </form>
<?php
    break;

    // executa insercao de especie no BD
    case 1:

        //Verifica a existencia de registros iguais
        if (comparaValor
                ("nom_especie", $nomEspecie,"patrimonio.especie", "and
                cod_natureza = '".$codNatureza."' and cod_grupo = '".$codGrupo."'",1)
            ){
            //Se não existir nenhum igual...
            include_once '../configPatrimonio.class.php';
            $objeto = "Espécie: ".$nomEspecie;
            $nId = pegaID("cod_especie","patrimonio.especie", "WHERE  cod_grupo = $codGrupo AND cod_natureza = $codNatureza");

            $patrimonio = new configPatrimonio;
            $patrimonio->setaVariaveisEspecie($codGrupo, $codNatureza, $nId, $nomEspecie);

            if ($patrimonio->insereEspecie()) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
                $audicao->insereAuditoria();
                echo '
                    <script type="text/javascript">
                        alertaAviso("'.$objeto.'","incluir","aviso","'.Sessao::getId().'");
                        mudaTelaPrincipal("incluiEspecie.php?'.Sessao::getId().'");
                    </script>';

            } else {
                echo '
                    <script type="text/javascript">
                        alertaAviso("'.$objeto.'","n_incluir","erro","'.Sessao::getId().'");
                        mudaTelaPrincipal("incluiEspecie.php?'.Sessao::getId().'");
                    </script>';
            }

            // insere atributos
            $cons = 0;
            while (list($chave,$valor) = each($atributo)) {
                $config = new configPatrimonio;
                $config->setaVariaveisAtributosEspecie($valor, $nId, $codGrupo, $codNatureza);
                if ($config->insereAtributosEspecie()) {
                    $cons = $cons;
                } else {
                    $cons = $cons++;
                }
            }

            if ($cons != 0) {
                echo '
                    <script type="text/javascript">
                        alertaAviso("Erro no cadastro de Atributos","unica","erro","'.Sessao::getId().'");
                        window.location = "incluiEspecie.php?'.Sessao::getId().'";
                    </script>';
                    $patrimonio->setaVariaveisEspecie($codGrupo, $codNatureza, $nId);
                    $patrimonio->deleteEspecie();
            }
        } else {
            //Se já existir algum registro com esse nome
            echo '
                <script type="text/javascript">
                    alertaAviso("Já existe uma espécie com esse nome","unica","erro","'.Sessao::getId().'");
                    window.location = "incluiEspecie.php?'.Sessao::getId().'&nomEspecie='.$nomEspecie.'&codGrupo='.$codGrupo.'&codNatureza='.$codNatureza.'";
                </script>';
        }

    break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
