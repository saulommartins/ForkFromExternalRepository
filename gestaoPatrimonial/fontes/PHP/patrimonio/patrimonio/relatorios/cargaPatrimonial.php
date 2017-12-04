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
    * Relatório de Carga Patrimonial
    * Data de Criação   : 11/04/2003

    * @author Desenvolvedor Ricardo Lopes de Alencar

    * @ignore

    $Revision: 17127 $
    $Name$
    $Autor: $
    $Date: 2006-10-24 16:17:05 -0300 (Ter, 24 Out 2006) $

    * Casos de uso: uc-03.01.13
*/

/*
$Log$
Revision 1.19  2006/10/24 19:17:05  hboaventura
bug #7102#

Revision 1.18  2006/07/21 11:36:18  fernando
Inclusão do  Ajuda.

Revision 1.17  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.16  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.13");
    Sessao::remove('filtro');
    $anoExercicio = pegaConfiguracao("ano_exercicio");
    include_once '../consulta.class.php';
    $consulta = new consulta;
    $consulta->setaVariaveisBens($codBem, $natu, $grup, $espec, $org, $unit, $dep, $set, $loc);

    $orgLista = $consulta->mostraOrgao();
    if (isset($org) and ($org != "xxx"))
    $uniLista = $consulta->mostraUnidade($org);
    if (isset($unit) and ($org != "xxx") AND ($unit != "xxx"))
    $depLista = $consulta->mostraDepto($org,$unit);
    if (isset($dep) and ($org != "xxx") AND ($unit != "xxx") AND ($dep != "xxx"))
    $setLista = $consulta->mostraSetor($org,$unit,$dep);
    if (isset($set) and ($org != "xxx") AND ($unit != "xxx") AND ($dep != "xxx") AND ($set != "xxx"))
    $locLista = $consulta->mostraLocal($org,$unit,$dep,$set);

            // buscara mascara do setor
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
        $mascaraSetor = pegaConfiguracao("mascara_local");

    // operacoes no frame oculto
        switch ($ctrl_frm) {
            // preenche os combos do Local
            case 1:
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosCALOLegado.inc.php';
                exit();
            break;

            // preenche Natureza, Grupo e Especie
            case 2:
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosNGE.inc.php';
                exit();
            break;
        }
?>

   <script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

      if (document.frm.codOrgao.value == 'xxx') {
          mensagem += "É necessário selecionar uma opção!";
          erro = true;
      }

       if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            return !(erro);
      }

      function Salvar()
      {
         if (Valida()) {
            if (document.frm.tipoRelatorio[0].checked) {
                document.frm.action = "cargaPatrimonialCompleto.php?<?=Sessao::getId()?>";
            }
            if (document.frm.tipoRelatorio[1].checked) {
                document.frm.action = "cargaPatrimonialCompleto.php?<?=Sessao::getId()?>";
            }
            if (document.frm.tipoRelatorio[2].checked) {
                document.frm.action = "cargaPatrimonialTotalizador.php?<?=Sessao::getId()?>";
            }
            document.frm.target = 'telaPrincipal';
            document.frm.submit();
         }
      }

            // preenche os combos do local (Orgao, Unidade, Departamento, Setor e Local)
            function preencheLocal(variavel, valor)
            {
                document.frm.target = "oculto";
                document.frm.ctrl.value = '1';
                document.frm.ctrl_frm.value = "1";
                document.frm.variavel.value = variavel;
                document.frm.valor2.value = escape(valor);
                document.frm.submit();
            }

            // preenche os combos de Natureza, Grupo e Especie
            function preencheNGE(variavel, valor)
            {
                document.frm.target = "oculto";
                document.frm.ctrl.value = '1';
                document.frm.ctrl_frm.value = "2";
                document.frm.variavel.value = variavel;
                document.frm.valor2.value = valor;
                document.frm.submit();
            }

            // desabilita botao 'OK' se o valor informado no input text nao existir e vice-versa
            // submete o formulario para preencher os campos dependentes ao combo selecionado
            function verificaCombo(campo_a, campo_b)
            {
                var aux;
                aux = preencheCampo(campo_a, campo_b);
                if (aux == false) {
                    document.frm.ok.disabled = true;
                } else {
                    document.frm.ok.disabled = false;
                }
                preencheNGE(campo_b.name, campo_b.value)
            }

   </script>
<form name="frm" action="<?=$PHP_SELF;?>?<?=Sessao::getId()?>" method="POST">

            <input type="hidden" name="ctrl" value=''>
            <input type="hidden" name="ctrl_frm" value=''>

<?php // os hiddens abaixo sao usados para a manipulacao dos filtros CALO e NGE. ?>
            <input type="hidden" name="variavel" value=''>
            <input type="hidden" name="valor2" value=''>

<table width=100%>

<tr><td class="alt_dados" colspan='2' heigth='5'>Centro de Custo</td></tr>

        <tr>
            <td class="label"  title="Informe a localização do bem." width="30%">*Localização</td>
            <td class="field">
                <input type="text" name="codMasSetor" value="<?=$codMasSetor?>" size="<?=strlen($mascaraSetor);?>" maxlength="<?=strlen($mascaraSetor);?>"
                    onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraSetor?>', this, event);"
                    onChange="JavaScript: preencheLocal( 'codMasSetor', this.value )">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o órgão do bem." width="30%">*Órgão</td>
            <td class='field'>
                <select name='codOrgao' onChange="javascript: preencheLocal('codOrgao', this.value);" style="width:400px">
                    <option value='xxx' SELECTED>Selecione</option>
<?php
                    //Faz o combo de Órgãos
                    $sSQL = "SELECT
                                cod_orgao, nom_orgao, ano_exercicio
                            FROM
                                administracao.orgao
                            ORDER
                                by nom_orgao";

                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();

                    $comboCodOrgao = "";
                    while (!$dbEmp->eof()) {
                        $anoExercicio = trim($dbEmp->pegaCampo("ano_exercicio"));
                        $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                        $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                        $chave = $codOrgaof."-".$anoExercicio;
                        $dbEmp->vaiProximo();
                        $comboCodOrgao .= "<option value='".$chave."'";
                        if (isset($codOrgao)) {
                            if ($chave == $codOrgao) {
                                $comboCodOrgao .= " SELECTED";
                                $nomOrgao = $nomOrgaof;
                            }
                        }
                        $comboCodOrgao .= ">".$nomOrgaof." - ".$anoExercicio."</option>\n";
                    }

                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboCodOrgao;
?>
                </select>
                <input type="hidden" name="nomOrgao" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione a Unidade do bem." width="30%">*Unidade</td>
            <td class="field">
                <select name="codUnidade" onChange="javascript: preencheLocal('codUnidade', this.value);" style="width:400px">
                    <option value=xxx SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomUnidade" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o departamento do bem." width="30%">*Departamento</td>
            <td class="field">
                <select name="codDepartamento" onChange="javascript: preencheLocal('codDepartamento', this.value);" style="width:400px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomDepartamento" value="">
            </td>
        </tr>
        <tr>
           <td class="label"  title="Selecione o setor do bem." width="30%">*Setor</td>
           <td class="field">
                <select name="codSetor" onChange="javascript: preencheLocal('codSetor', this.value);" style="width:400px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomSetor" value="">
                <input type="hidden" name="anoExercicioSetor" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o local do bem." width="30%">*Local</td>
            <td class="field">
                <select name="codLocal" onChange="javascript: preencheLocal('codLocal', this.value);" style="width:400px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomLocal" value="">
                <input type="hidden" name="anoExercicioLocal" value="">
            </td>
        </tr>
    <tr>
        <td class="alt_dados" colspan=2>Opções</td>
    </tr>
    <tr>
        <td class=label title="Informe o tipo de relatório.">Tipo de Relatório</td>
        <td class=field>
            &nbsp;<input type="radio" name="tipoRelatorio" value="0" checked>Completo com totalizador
            <br>&nbsp;<input type="radio" name="tipoRelatorio" value="1">Cadastral com totalizador
            <br>&nbsp;<input type="radio" name="tipoRelatorio" value="2">Somente totalizador
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2"><?php geraBotaoOk(); ?></td>
    </tr>
</table></form>
<?php
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html
?>
