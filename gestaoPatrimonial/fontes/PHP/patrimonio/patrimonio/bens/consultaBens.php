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
    * Arquivo que seleciona o método de consulta
    * Data de Criação   : 27/03/2003

    * @author Desenvolvedor Leonardo Tremper

    * @ignore

    $Revision: 28299 $
    $Name$
    $Autor: $
    $Date: 2008-02-29 14:31:59 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.55  2007/05/22 15:13:12  hboaventura
Bug na consulta dos bens

Revision 1.54  2007/05/15 19:52:34  leandro.zis
Bug #8836#

Revision 1.53  2007/03/21 15:13:09  hboaventura
Bug #8831#

Revision 1.52  2006/10/11 15:45:01  larocca
Bug #5351#

Revision 1.51  2006/07/21 11:35:07  fernando
Inclusão do  Ajuda.

Revision 1.50  2006/07/18 14:31:31  fernando
diminuição dos campos na interface(eles agora estão mais próximo da margem direta)

Revision 1.49  2006/07/18 13:08:28  fernando
alteração de hint

Revision 1.48  2006/07/13 19:12:08  fernando
Especialização na consulta da descrição de bem

Revision 1.47  2006/07/13 18:34:58  fernando
Alteração de hints

Revision 1.46  2006/07/12 19:27:20  gelson
Correção dos hints

Revision 1.45  2006/07/07 18:21:55  fernando
Bug #6437

Revision 1.44  2006/07/06 18:35:01  fernando
Bug #5351

Revision 1.43  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once (CAM_GP."javaScript/ifuncoesJsGP.js");
setAjuda("UC-03.01.06");
if (!isset($ctrl)) {
    $ctrl = 1;
}

if (isset($pagina)) {
    $ctrl = '1.1';
}

switch ($ctrl) {

    case 1:

        unset($sessao->transf2);
        unset($sessao->transf4);

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
            case 3:
                // busca nome do fornecedor atraves do cod_fornecedor informado
                if ($fornecedor>0) {
                $sql = "SELECT
                            c.numcgm, c.nom_cgm
                        FROM
                            sw_cgm as c
                        WHERE
                            c.numcgm > 0
                            AND c.numcgm =".$fornecedor;
                $conn = new dataBaseLegado;
                $conn->abreBD();
                $conn->abreSelecao($sql);
                $conn->vaiPrimeiro();

                $sFornecedor  = trim($conn->pegaCampo("nom_cgm"));

                $conn->limpaSelecao();
                $conn->fechaBD();

                if (strlen($sFornecedor) > 0) {
                    $js .= 'd.getElementById("sFornecedor").innerHTML = "'.$sFornecedor.'";';
                } else {
                    $js .= 'f.fornecedor.value = "" ;';
                    $js .= 'd.getElementById("sFornecedor").innerHTML = "&nbsp;";';
                    $js .= "erro = true;\n";
                    $js .= 'mensagem += "Número do CGM inválido!(Código: '.$fornecedor.').";';
                    $js .= 'f.fornecedor.focus()';
                }
                } else {
                    $js .= 'd.getElementById("sFornecedor").innerHTML = "&nbsp;";';
                }
                executaFrameOculto($js);

                exit();
            break;
        }
        // encerra operacoes no frame oculto

        include_once '../consulta.class.php';
        $consulta = new consulta;
?>
       <script type="text/javascript">

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campo2;
                var campo3;
                var campo4;
                var campo5;
                var campoaux;

                campo = document.frm.codBem.value.length;
                campo2 = document.frm.descricao.value.length;
                campo3 = document.frm.codNatureza.value;
                campo4 = document.frm.codGrupo.value;
                campo5 = document.frm.codEspecie.value;
                campo6 = document.frm.codOrgao.value;
                campo7 = document.frm.codUnidade.value;
                campo8 = document.frm.codDepartamento.value;
                campo9 = document.frm.codSetor.value;
                campo10 = document.frm.codLocal.value;
                campo11 = document.frm.numApolice.value;
                campo12 = document.frm.numCgm.value;
                campo13 = document.frm.datainicial.value.length;
                campo14 = document.frm.datafinal.value.length;
                campo15 = document.frm.numPlaca.value.length;
                campo16 = document.frm.fornecedor.value.length;
                campo17 = document.frm.codEmpenho.value.length;
                campo18 = document.frm.numNotaFiscal.value.length;
                campo19 = document.frm.codEntidade.value;
                campo20 = document.frm.codTxtSituacao.value.length;
                campo21 = document.frm.descSituacao.value.length;
                campo22 = document.frm.exercicioEmpenho.value.length;

                if ( (campo == 0) && (campo2 == 0) && (campo3 == "xxx") && (campo4 == "xxx") &&  (campo5 == "xxx") && (campo6 == "xxx") && (campo7 == "xxx") &&  (campo8 == "xxx") && (campo9 == "xxx") && (campo10 == "xxx")  && (campo11 == "") && (campo12 == "xxx") && (campo13 == 0) && (campo14 == 0) && (campo15 == 0) && (campo16 == 0) && (campo17 == 0) &&  (campo18 == 0) && (campo19 == "xxx")  && (campo20 == 0)&&  (campo21 == 0) && (campo22 == 0) ) {

                    mensagem += "@Selecione ao menos uma opção!";
                    erro = true;
                }

                if (erro == false && ((campo13 != 0) || (campo14 != 0))) {
                    if (campo13 == 0) {
                        mensagem += "@Informe a Data Inicial!";
                        erro = true;
                    }

                    if (campo14 == 0) {
                        mensagem += "@Informe a Data Final!";
                        erro = true;
                    }
                }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);
            }

            function Salvar()
            {
                if (Valida()) {
                    document.frm.target = "telaPrincipal";
                    document.frm.ctrl.value = '1.1';
                    document.frm.action = "consultaBens.php?<?=Sessao::getId();?>&ctrl=1.1";
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

            function busca_fornecedor()
            {
                document.frm.action = "consultaBens.php?<?=Sessao::getId()?>";

                document.frm.ctrl.value = 1;
                document.frm.ctrl_frm.value = 3;
                var f = document.frm;
                f.target = 'oculto';
                f.submit();
            }
            function Limpar()
            {
                document.getElementById("sFornecedor").innerHTML = "&nbsp;";
                document.frm.reset();
            }

//            function busca_entidades(cod) {
//               document.frm.action = "consultaBens.php?<?=Sessao::getId()?>";
//               document.frm.ctrl.value = 6;
//               document.frm.ctrl_frm.value = 3;
//               var f = document.frm;
//               f.target = 'oculto';
//               f.submit();
//            }

        </script>

        <form name="frm" action="consultaBens.php?<?=Sessao::getId();?>" method="POST">
            <input type="hidden" name="ctrl" value=''>
            <input type="hidden" name="ctrl_frm" value=''>

<?php // os hiddens abaixo sao usados para a manipulacao dos filtros CALO e NGE. ?>
            <input type="hidden" name="variavel" value=''>
            <input type="hidden" name="valor2" value=''>

        <table width="100%">
        <tr><td class="alt_dados" colspan='2' height='5'>Classificação</td></tr>

        <tr>
            <td class="label" width="20%" title="Selecione a natureza do bem.">Natureza</td>
            <td class='field' width="80%">
            <!--
                <input type="text" name="codTxtNatureza"
                    value="<?=$codNatureza != "xxx" ? $codNatureza : "";?>" size="10" maxlength="10"
                    onChange="javascript: verificaCombo(this, document.frm.codNatureza);"
                    onKeyPress="return(isValido(this, event, '0123456789'));">
            -->
                <select name='codNatureza' onChange="javascript: preencheNGE('codNatureza', this.value);" style="width:300px">
                    <option value='xxx' SELECTED>Selecione</option>
<?php
                    // busca Naturezas cadastradas
                    $sSQL = "SELECT
                                cod_natureza, nom_natureza
                            FROM
                                patrimonio.natureza
                            ORDER
                                by nom_natureza";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();

                    // monta combo com Naturezas
                    $comboCodNatureza = "";

                    while (!$dbEmp->eof()) {

                        $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
                        $nomNaturezaf  = trim($dbEmp->pegaCampo("nom_natureza"));
                        $chave = $codNaturezaf;
                        $dbEmp->vaiProximo();

                        $comboCodNatureza .= "<option value='".$chave."'";

                        if (isset($codNatureza)) {
                            if ($chave == $codNatureza) {
                                $comboCodNatureza .= " SELECTED";
                                $nomNatureza = $nomNaturezaf;
                            }
                        }

                        $comboCodNatureza .= ">".$nomNaturezaf."</option>\n";
                    }

                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();

                    echo $comboCodNatureza;
?>
                </select>
                <input type="hidden" name="nomNatureza" value="">
            </td>
        </tr>

        <tr>
            <td class="label" width="20%" title="Selecione o grupo do bem.">Grupo</td>
            <td class="field">
            <!--
                <input type="text" name="codTxtGrupo"
                    value="<?=$codGrupo != "xxx" ? $codGrupo : "";?>" size="10" maxlength="10"
                    onChange="javascript: verificaCombo(this, document.frm.codGrupo);"
                    onKeyPress="return(isValido(this, event, '0123456789'));">
            -->
                <select name="codGrupo" onChange="javascript: preencheNGE('codGrupo', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>

                <input type="hidden" name="nomGrupo" value="">
            </td>
        </tr>

        <tr>
            <td class="label" width="20%" title="Selecione a espécie do bem.">Espécie</td>
            <td class="field">
            <!--
                <input type="text" name="codTxtEspecie"
                    value="<?=$codEspecie != "xxx" ? $codEspecie : "";?>" size="10" maxlength="10"
                    onChange="javascript: verificaCombo(this, document.frm.codEspecie);"
                    onKeyPress="return(isValido(this, event, '0123456789'));">
            -->
                <select name="codEspecie" onChange="javascript: preencheNGE('codEspecie', this.value);" style="width:300px" >
                    <option value="xxx" SELECTED>Selecione</option>
                </select>

                <input type="hidden" name="nomEspecie" value="">
            </td>
        </tr>
        <tr>
            <td class="alt_dados" colspan="2">Informações Básicas</td>
        </tr>
        <input type="hidden" name="valorBem" value="<?=$codBem;?>" >
        <tr>
            <td class="label" width="20%" title="Informe o código do bem.">Código do Bem</td>
            <td class="field">
                <input type="text" name="codBem" value="<?=$codBem;?>"  onKeyPress="javascript:return(isValido(this, event, '0123456789'));">&nbsp;
                <a href="javascript:procuraBemGP('frm','codBem','<?=Sessao::getId()?>');">
                <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Procurar bem" width=20 height=20 border=0 align="absmiddle"></a>
            </td>
        </tr>

        <tr>
            <td class="label" width="20%" title="Informe a descrição do bem.">Descrição</td>
            <td class="field">
                <input type="text" name="descricao" size="79.5" maxlength="60"  value="<?=$codBem;?>">
                <select name="stTipoBuscaDescricaoBem" value="inicio">
                    <option value="inicio" selected="selected">Início</option>
                    <option value="final">Final</option>
                    <option value="contem">Contém</option>
                    <option value="exata">Exata</option>
                </select>
            </td>
        </tr>

        <tr>
            <td class="label" width="20%" title="Informe o detalhamento do Bem.">Detalhamento</td>
            <td class="field">
                <textarea name='detalhamento' cols='40'  rows='4'></textarea>
            </td>
        </tr>

       <tr>
            <td class="label" title="Informe o código do fornecedor do bem.">Fornecedor</td>
            <td class="field">

                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                <tr>
                <td align="left" width="11%" valign="top">
                    <input type='text' id='fornecedor' name='fornecedor'
                     size='10' maxlength='10' onBlur="busca_fornecedor(1);"
                    onKeyPress="return(isValido(this, event, '0123456789'))">
                    <input type="hidden" name="Hdnfornecedor">
                    <input type="hidden" name="sFornecedor">
                </td>
                <td width="1">&nbsp;</td>
                <td align="left" width="48%" id="sFornecedor" name="sFornecedor" class="fakefield" valign="middle">&nbsp;</td>

                <td align="left" valign="top">
                    &nbsp;
                    <a href="javascript:procurarCgm('frm','fornecedor','sFornecedor','juridica','<?=Sessao::getId()?>',1)">
                    <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar fornecedor" border="0" align="absmiddle"></a>
                    </a>
                </td>
                </tr>
                </table>

            </td>
        </tr>

        <tr>
            <td class="label"  title="Informe o valor do bem.">Valor do Bem</td>
            <td class="field">
                <input type="text" name="valorBem" value="<?=$valorBem;?>" size='14' maxlength='13'
                    onKeyPress="return validaCharMoeda( this, event );"
                    onBlur="return formataMoeda(this, '2', event);"
                    onKeyUp="return mascaraMoeda(this, '2', event);"
                >
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o valor da depreciação." >Valor da Depreciação</td>
            <td class="field"><input type="text" name="valorDepreciacao" value="<?=$valorDepreciacao;?>" size='14' maxlength='13'
                    onKeyPress="return validaCharMoeda( this, event );"
                    onBlur="return formataMoeda(this, '2', event);"
                    onKeyUp="return mascaraMoeda(this, '2', event);"
            >
          </td>
        </tr>
        <?php
         geraCampoData2("Data da Depreciação", "dataDepreciacao", $dataDepreciacao, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data da depreciação do bem","Buscar data da depreciação" );
         geraCampoData2("Data da Aquisição", "dataAquisicao", $dataAquisicao, false, "onChange=\"JavaScript: if (!validaGarantia(document.frm.dataGarantia.value ,this.value)){alertaAviso('@Data da Garantia precisa ser posterior à data deAquisição!','form','erro','Sessao::getId()');this.value='';};\"  onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if(!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data da aquisição","Buscar data da aquisição" );
         geraCampoData2("Vencimento da Garantia", "dataGarantia", $dataGarantia, false, "onChange=\"JavaScript: if (!validaGarantia(this.value,document.frm.dataAquisicao.value)){alertaAviso('@Data da Garantia precisa ser posterior à data da Aquisição!','form','erro','Sessao::getId()');this.value='';};\"   onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if(!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data do vencimento da garantia do bem","Buscar data do vencimento" );
        ?>

        <tr>
            <td class="label" width="20%" title="Informe o número da placa do bem.">Número da Placa</td>
            <td class="field">
                <input type="text" name="numPlaca" value="<?=$numPlaca;?>">
                <select name="stTipoBuscaDescricao" value="inicio">
                    <option value="inicio" selected="selected">Início</option>
                    <option value="final">Final</option>
                    <option value="contem">Contém</option>
                    <option value="exata">Exata</option>
                </select>
            </td>
        </tr>

        <tr><td class="alt_dados" colspan='2' height='5'>Informações Financeiras</td></tr>

       <tr>
            <td class="label" title="Selecione a entidade do bem.">Entidade</td>
            <td class="field">                 <select name="codEntidade">
                   <option value='xxx' SELECTED>Selecione</option>
<?php

            $sSQL = "SELECT
                        C.numcgm,
                        C.nom_cgm
                    FROM
                        sw_cgm as C,
                        orcamento.entidade AS OE
                    WHERE
                        C.numcgm = OE.numcgm
                    GROUP BY
                        C.numcgm
                        ,C.nom_cgm
                    ORDER BY
                        C.nom_cgm ";

                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                            // monta combo com Entidade
                     while (!$dbEmp->eof()) {
                        $arEntidade[$dbEmp->pegaCampo('numcgm')]['nom_cgm'] = $dbEmp->pegaCampo('nom_cgm');
                        $dbEmp->vaiProximo();
                     }

                     $comboEntidade = "";
                     foreach ($arEntidade as $key => $entidade) {
                        $comboEntidade .= ' <option value='.$key;
                        if (isset($codEntidade)) {
                            if ($entidade['cod_entidade'] == $key) {
                                 $comboEntidade .= " SELECTED";
                            }
                        }
                        $comboEntidade .= ">".$entidade['nom_cgm'] . "</option>";
                     }
                     echo($comboEntidade);

?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o exercício do empenho do bem." >Exercício do Empenho</td>
            <td class="field">
               <input type="text" name="exercicioEmpenho" value='<?=$exercicioEmpenho;?>' size='5' maxlength='4'>
            </td>
        </tr>

        <tr>
            <td class="label" title="Informe o número do empenho do bem." >Número do Empenho</td>
            <td class="field">
                <input type="text" name="codEmpenho" value="<?=$codEmpenho;?>" size='10' maxlength='9' onKeyUp="return autoTab(this, 9, event);" onKeyPress="return(isValido(this, event,
'0123456789'));">
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o número da nota fiscal do bem.">Número da Nota Fiscal</td>
            <td class="field">
                <input type="text" name="numNotaFiscal" value="<?=$numNotaFiscal;?>" size='21' maxlength='20' >
            </td>
        </tr>

        <tr><td class="alt_dados" colspan='2' height='5'>Histórico</td></tr>

        <tr>
            <td class="label"  title="Informe a localização do bem." width="20%">Localização</td>
            <td class="field">
                <input type="text" name="codMasSetor" value="<?=$codMasSetor?>" size="<?=strlen($mascaraSetor);?>" maxlength="<?=strlen($mascaraSetor);?>"
                    onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraSetor?>', this, event);"
                    onChange="JavaScript: preencheLocal( 'codMasSetor', this.value )">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o orgão do bem." width="20%">Orgão</td>
            <td class='field'>
                <select name='codOrgao' onChange="javascript: preencheLocal('codOrgao', this.value);" style="width:300px">
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
            <td class="label"  title="Selecione a unidade do bem." width="20%">Unidade</td>
            <td class="field">
                <select name="codUnidade" onChange="javascript: preencheLocal('codUnidade', this.value);" style="width:300px">
                    <option value=xxx SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomUnidade" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o departamento do bem." width="20%">Departamento</td>
            <td class="field">
                <select name="codDepartamento" onChange="javascript: preencheLocal('codDepartamento', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomDepartamento" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o setor do bem." width="20%">Setor</td>
            <td class="field">
                <select name="codSetor" onChange="javascript: preencheLocal('codSetor', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomSetor" value="">
                <input type="hidden" name="anoExercicioSetor" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o local do bem." width="20%">Local</td>
            <td class="field">
                <select name="codLocal" onChange="javascript: preencheLocal('codLocal', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomLocal" value="">
                <input type="hidden" name="anoExercicioLocal" value="">
            </td>
        </tr>

        <?php
        include_once 'interfaceBens.class.php';
        $interfaceBens = new interfaceBens();

        ?>
         <td class="label" title="Informe a situação do bem." >Situação</td>
            <td class="field">
                <input type="text" name="codTxtSituacao" value="<?=$situacao != "xxx" ? $situacao : "";?>" size="10" maxlength="10"
                onChange="javascript: preencheCampo(this, document.frm.situacao);"
                onKeyPress="return(isValido(this, event, '0123456789'));">
                <?echo $interfaceBens->comboSituacao("situacao",$situacao);?>
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe a descrição da situação do bem.">Descrição da Situação</td>
            <td class="field">
                <input type="text"  name="descSituacao" value="<?=$descSituacao;?>" size="77" maxlength="60" >
                <select name="stTipoBuscaDescricaoSituacao" value="inicio">
                    <option value="inicio" selected="selected">Início</option>
                    <option value="final">Final</option>
                    <option value="contem">Contém</option>
                    <option value="exata">Exata</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="alt_dados" colspan="2">Bens Segurados</td>
        </tr>
       <tr>
            <td class="label" title="Selecione o nome da seguradora.">Seguradora</td>
            <td class="field">
<?php
        //    include_once "../apolice.class.php";
        //    $bemsegurado = new apolice;
        //    $bemsegurado->listaComboSeguradoras();
        //    $bemsegurado->mostraComboSeguradoras();
        $sSQL = "SELECT DISTINCT a.numcgm, c.nom_cgm FROM patrimonio.apolice as a, sw_cgm as c
                WHERE a.numcgm = c.numcgm
                ORDER by nom_cgm";
        $dbEmp = new dataBaseLegado;         $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);         $dbEmp->vaiPrimeiro();
        $comboSeguradoras = "";
        $comboSeguradoras .= "<select name=numCgm style='width:200px' onChange=\"javascript: if(this.value!='xxx') numApolice.disabled = false; else { numApolice.value=''; numApolice.disabled = true} \" >\n<option value=xxx SELECTED>Selecione</option>\n";
        while (!$dbEmp->eof()) {
            $numcgm  = trim($dbEmp->pegaCampo("numcgm"));
            $nomCgm  = trim($dbEmp->pegaCampo("nom_cgm"));
            $dbEmp->vaiProximo();
            $comboSeguradoras .= "<option value=".$numcgm.">".$nomCgm."</option>\n";
        }
        $comboSeguradoras .= "</select>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $comboSeguradoras;
?>
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o número da apólice">Número da Apólice</td>
            <td class="field">
                <input type="text" name="numApolice" onKeyPress="javascript:return(isValido(this, event, '0123456789'));" size="20" maxlength="20" disabled>
            </td>
        </tr>

        <tr>
            <td class="alt_dados" colspan="2">Bens para Manutenção</td>
        </tr>
<?php
 geraCampoData2("Data Inicial", "datainicial", $datainicial,false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript:if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')' ,'form','erro','Sessao::getId()');this.value='';};\"","Informe a data inicial","Buscar data inicial" );
 geraCampoData2("Data Final",   "datafinal"  , $datafinal,  false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript:if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')' ,'form','erro','Sessao::getId()');this.value='';};\"","Informe a data final","Buscar data final" );
?>
        <tr>
            <td class="label" title="Selecione a ordenação dos bens.">Ordenar Por</td>
            <td class="field">
                <select name="ordenar">
                    <option value="b.cod_bem" SELECTED>Código do Bem</option>
                    <option value="e.nom_especie">Espécie</option>
                    <option value="b.descricao">Descrição</option>
                </select>
            </td>
        </tr>

        <tr>
            <td colspan='2' class='field'>
            <?php geraBotaoOk2(); ?>
            </td>
        </tr>

        </table>

        </form>
<?php
    break;

//          case 6:
//                     include_once "../bens.class.php";
//                     $tmp = new bens;
//                     $arEntidade = $tmp->listaEntidades($exercicioEmpenho);
//                     unset($tmp);
//
//                     $i = 1;
//                     $js .= 'f.codEntidade.options.length = 1;';
//                     foreach ($arEntidade as $entidade) {
//                        $js .= 'f.codEntidade.options['.$i.'] = new Option(\''.$entidade['nom_entidade'].'\',\'';
//                        $js .= $entidade['cod_entidade'].'\');';
////                        if (isset($codEntidade)) {
////                            if ($entidade['cod_entidade'] == $codEntidade) {
////                                 $js .= 'f.codEntidade.options.selectedIndex = '.$i.';';
////                            }
////                        }
//                        $i++;
//                     }
//
//
//               executaFrameOculto($js);
//               exit();
//            break;
//

/************************************************************/
// exibe resultado da consulta por Bens
/************************************************************/
    case 1.1:
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';

        while ( list( $key, $val ) = each( $HTTP_POST_VARS ) ) {
            $variavel = $key;
            $$variavel = $val;
            $aVarWhere[$key] = $val;

            switch ($key) {

                // monta tabelas dinamicas para BENS SEGURADOS
                case "numCgm":
                    if ($val>0) {
                        $tbCgm  = "sw_cgm as cg,";
                        $tbCgm .= "patrimonio.apolice as ap,";
                        $tbCgm .= "patrimonio.apolice_bem as apb,";
                        $whCgm  = " AND cg.numcgm = ap.numcgm";
                        $whCgm .= " AND ap.cod_apolice = apb.cod_apolice";
                        $whCgm .= " AND b.cod_bem = apb.cod_bem";
                        $whCgm .= " AND cg.numcgm = ".$val;
                    }
                break;

                case "codEntidade":
                        $tbEntidade = " LEFT outer join orcamento.entidade as oe on (bc.exercicio = oe.exercicio AND bc.cod_entidade = oe.cod_entidade)";
                    if ($val != 'xxx') {
                        $whEntidade .=" AND oe.numcgm = $val";
                    }
                break;

                case "codEmpenho":
                    if ($val != '') {
                        $whEntidade .=" AND bc.cod_empenho = $val";
                    }
                break;
                case "exercicioEmpenho":
                    if ($val != '') {
                        $whEntidade .=" AND bc.exercicio = '".$val."'";
                    }
                break;

                case "numNotaFiscal":
                    if ($val != '') {
                        $whEntidade .=" AND bc.nota_fiscal = '".$val."'";
                    }
                break;
                $historicoInner = 'false';
                case "codTxtSituacao":
                    if ($val>0) {
                        $historicoInner = 'true';
                        $whHistorico .= " AND hb.cod_situacao = $val";
                    }
                break;
                case "codOrgao" :
                    if ($val != 'xxx') {
                        $historicoInner = 'true';
                        $val = explode('-',$val);
                        $exercicio = $val[1];
                        $val = trim($val[0]);
                        $whHistorico .= " AND hb.ano_exercicio = '".$exercicio."'";
                        $whHistorico .= " AND hb.cod_orgao = $val";
                    }
                break;
                case "codUnidade" :
                    if ($val != 'xxx') {
                        $historicoInner = 'true';
                        $val = explode('-',$val);
                        $val = trim($val[0]);
                        $whHistorico .= " AND hb.cod_unidade = $val";

                    }
                break;
                case "codDepartamento" :
                    if ($val != 'xxx') {
                        $historicoInner = 'true';
                        $val = explode('-',$val);
                        $val = trim($val[0]);
                        $whHistorico .= " AND hb.cod_departamento = $val";
                    }

                break;

                case "codSetor" :
                    if ($val != 'xxx') {
                        $historicoInner = 'true';
                        $val = explode('-',$val);
                        $val = trim($val[0]);
                        $whHistorico .= " AND hb.cod_setor = $val";
                    }
                break;

                case "codLocal" :
                    if ($val != 'xxx') {
                        $historicoInner = 'true';
                        $val = explode('-',$val);
                        $val = trim($val[0]);
                        $whHistorico .= " AND hb.cod_local = $val";
                    }
                break;

                case "descSituacao":
                    if ($val != '') {
                        $historicoInner = 'true';

                        switch ($stTipoBuscaDescricaoSituacao) {
                            case 'inicio':
                                $whHistorico .= " AND lower(ltrim(hb.descricao,0)) like lower('".ltrim($val,0)."')||'%' ";
                            break;
                            case 'final':
                                $whHistorico .= " AND lower(ltrim(hb.descricao,0)) like '%'||lower('".ltrim($val,0)."') ";
                            break;
                            case 'contem':
                                $whHistorico .= " AND lower(ltrim(hb.descricao,0)) like '%'||lower('".ltrim($val,0)."')||'%' ";
                            break;
                            case 'exata':
                                $whHistorico .= " AND lower(ltrim(hb.descricao,0)) = lower('".ltrim($val,0)."') ";
                            break;
                        }
                    }
                break;

                // monta tabelas dinamicas para BENS EM MANUTENCAO
                case "datainicial":
                    if ($val != "") {
                        $tbData    = "patrimonio.manutencao as m,";
                        $dtInicial = dataToSql($datainicial);
                        $dtFinal   = dataToSql($datafinal);

                        // a data Final deve ser maior que a data Inicial
                        if ($dtFinal < $dtInicial) {

                            echo '
                                <script type="text/javascript">
                                    alertaAviso("Data final não pode ser menor que data inicial","unica","erro","'.Sessao::getId().'");
                                    window.location = "consultaBens.php?'.Sessao::getId().'&ctrl=1";
                                </script>';

                        } else {
                            $whData    = " and m.cod_bem = b.cod_bem";
                            $whData   .= " and m.dt_agendamento BETWEEN '".$dtInicial."' AND '".$dtFinal."'";
                        }
                    }
                break;
            }
        }

        function MontaWhere()
        {
            global $aVarWhere;
            $i = 0;

            while ( list($key , $val) = each($aVarWhere) ) {
                $variavel  = $key;
                $$variavel = $val;

                if ( $val<>"xxx" AND trim($val) <> "" ) {
                    switch ($key) {
                        case "codBem" :
                            $sCampo      = "b.cod_bem";
                            $aCampos[$i] = array($sCampo,"N",$val,"=");
                            $i++;
                        break;
                        case "codNatureza" :
                            $sCampo      = "b.cod_natureza";
                            $aCampos[$i] = array($sCampo,"N",$val,"=");
                            $i++;
                        break;
                        case "codGrupo" :
                            $sCampo      = "b.cod_grupo";
                            $aCampos[$i] = array($sCampo,"N",$val,"=");
                            $i++;
                        break;
                        case "codEspecie" :
                            $sCampo      = "b.cod_especie";
                            $aCampos[$i] = array($sCampo,"N",$val,"=");
                            $i++;
                        break;
                    }
                }
            }

            $i=0;
            $sWhere = "";

            while ( $i < sizeof($aCampos) ) {
                if ($aCampos[$i][1] == "T") {
                    if ($aCampos[$i][3] == "L") {
                        $sParte = $aCampos[$i][0]." like '%".$aCampos[$i][2]."%'";
                    } else {
                        $sParte = $aCampos[$i][0].$aCampos[$i][3]."'".$aCampos[$i][2]."'";
                    }
                } else {
                    $sParte = $aCampos[$i][0].$aCampos[$i][3].$aCampos[$i][2];
                }

                if ( strlen($sWhere)>0 ) {
                    $sWhere = $sWhere." AND ".$sParte;
                } else {
                    $sWhere = $sParte;
                }
                $i++;
            }

            if ( strlen($sWhere)>0 ) {
                $sWhere = " AND ".$sWhere;
            }

            if ( trim($descricao) != "" ) {
                switch ($stTipoBuscaDescricaoBem) {
                    case 'inicio':
                        $sWhere .= " AND lower(ltrim(B.descricao,0)) like lower('".ltrim($descricao,0)."')||'%' ";
                    break;
                    case 'final':
                        $sWhere .= " AND lower(ltrim(B.descricao,0)) like '%'||lower('".ltrim($descricao,0)."') ";
                    break;
                    case 'contem':
                        $sWhere .= " AND lower(ltrim(B.descricao,0)) like '%'||lower('".ltrim($descricao,0)."')||'%' ";
                    break;
                    case 'exata':
                        $sWhere .= " AND lower(ltrim(B.descricao,0)) = lower('".ltrim($descricao,0)."') ";
                    break;
                }

            }
            if ( trim($fornecedor) != "") {
               $sWhere .= " AND b.numcgm = ".$fornecedor;
            }
            if ( trim($numPlaca) != "" ) {
                switch ($stTipoBuscaDescricao) {
                    case 'inicio':
                        $sWhere .= " AND lower(ltrim(B.num_placa,0)) like lower('".ltrim($numPlaca,0)."')||'%' ";
                    break;
                    case 'final':
                        $sWhere .= " AND lower(ltrim(B.num_placa,0)) like '%'||lower('".ltrim($numPlaca,0)."') ";
                    break;
                    case 'contem':
                        $sWhere .= " AND lower(ltrim(B.num_placa,0)) like '%'||lower('".ltrim($numPlaca,0)."')||'%' ";
                    break;
                    case 'exata':
                        $sWhere .= " AND lower(ltrim(B.num_placa,0)) = lower('".ltrim($numPlaca,0)."') ";
                    break;
                }

            }

            return $sWhere;
        }

        // ao executar o formulario de pesquisa pela primeira vez, $sWhere é atribuido a sessao
        if ( !isset($sessao->transf2) ) {
            $sWhere = MontaWhere();
            $sessao->transf4 = $sWhere;
        }

        // se $sWhere estiver vazio ele recebe o valor dele que esta guardado na sessao
        if ($sWhere == "") {
            $sWhere = $sessao->transf4;
        }

//sql comentado para listar os bens baixados também
        $sSQLs = "
SELECT
    b.cod_bem,
    b.num_placa,
    b.descricao,
    e.nom_especie
FROM
    ".$tbCgm."
    ".$tbApol."
    ".$tbData."
     patrimonio.natureza as n,
     patrimonio.grupo    as g,
     patrimonio.especie  as e,
     patrimonio.bem      as b
LEFT OUTER JOIN patrimonio.bem_baixado as bb ON
    bb.cod_bem = b.cod_bem
LEFT OUTER JOIN
     patrimonio.bem_comprado as bc on
     bc.cod_bem = b.cod_bem
    ".$tbEntidade."\n";

$sSQLs .= $historicoInner == 'false'? "LEFT OUTER" : "INNER";

$sSQLs .="
 JOIN (SELECT
                    max(hb.timestamp)
                    ,hb.cod_bem
                 FROM
                     patrimonio.historico_bem as hb\n";

$sSQLs .= $historicoInner == 'false'? "" : "WHERE 1 = 1 ";

$sSQLs .="
               $whHistorico
                  GROUP BY
                    hb.cod_bem
                ) as hb ON ( hb.cod_bem = b.cod_bem )
WHERE
     b.cod_especie  = e.cod_especie
AND  b.cod_grupo    = e.cod_grupo
AND  b.cod_natureza = e.cod_natureza
AND e.cod_grupo    =  g.cod_grupo
AND e.cod_natureza = g.cod_natureza
AND g.cod_natureza = n.cod_natureza
";

if ($_POST['detalhamento'] != "") {
    $sSQLs .= " AND lower(ltrim(B.detalhamento,0)) like '%'||lower('".ltrim($_POST["detalhamento"],0)."')||'%' ";
}
if ($_POST['valorBem'] != "") {
    $vlBem = str_replace(".","",$_POST['valorBem']);
    $vlBem = str_replace(",",".",$vlBem);
    $sSQLs .= " AND b.vl_bem = '".$vlBem."' ";
}
if ($_POST['valorDepreciacao'] != "") {
    $vlDep = str_replace(".","",$_POST['valorDepreciacao']);
    $vlDep = str_replace(",",".",$vlDep);
    $sSQLs .= " AND b.vl_depreciacao = '".$vlDep."' ";
}
if ($_POST['dataDepreciacao'] != "") {
    $data = explode('/',$_POST['dataDepreciacao']);
    $dtDep = $data[2]."-".$data[1]."-".$data[0];
    $sSQLs .= " AND b.dt_depreciacao = '".$dtDep."' ";
}
if ($_POST['dataAquisicao'] != "") {
    $data = explode('/',$_POST['dataAquisicao']);
    $dtAqu = $data[2]."-".$data[1]."-".$data[0];
    $sSQLs .= " AND b.dt_aquisicao = '".$dtAqu."' ";
}
if ($_POST['dataGarantia'] != "") {
    $data = explode('/',$_POST['dataGarantia']);
    $dtGar = $data[2]."-".$data[1]."-".$data[0];
    $sSQLs .= " AND b.dt_garantia = '".$dtGar."' ";
}
$sSQLs .= $sWhere.$whCgm.$whData.$whEntidade;

        if (!($numApolice == "" & $tblApol == "")) {
          $sSQLs .= " AND ap.num_apolice =".$numApolice." ";
        }

        //essao->transf2 = "";
        if (!$sessao->transf2) {
            $sessao->transf2 = $sSQLs;
        } else {
            $sSQLs = $sessao->transf2;
        }
        $sessao->transf3 = "";
        $sessao->transf3 = "b.cod_bem";
        $sessao->transf5 = "";

        $botoesPDF = new botoesPdfLegado;
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados( $sSQLs, "10" );
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("b.cod_bem","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
?>
        <table width=100%>
        <tr>
            <td class="alt_dados" colspan="5">Registros de Bens</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%">&nbsp;</td>
            <td class="labelcenter" width="7%">Código</td>
            <td class="labelcenter" width="30%">Espécie</td>
            <td class="labelcenter" width="53%">Descrição</td>
            <td class="labelcenter" width="5%">&nbsp;</td>
        </tr>
<?php
        if ($dbEmp->numeroDeLinhas == 0) {
?>
            <tr>
                <td class="show_dados_center" colspan="5"><b>Nenhum registro encontrado.</b></td>
            </tr>
<?php
        } else {

            $cont = $paginacao->contador();

            while (!$dbEmp->eof()) {
                $codBem  = trim($dbEmp->pegaCampo("cod_bem"));
                $nomEspecie  = trim($dbEmp->pegaCampo("nom_especie"));
                $descricao  = trim($dbEmp->pegaCampo("descricao"));
                $dbEmp->vaiProximo();
?>
                <tr>
                    <td class="labelcenter"><?=$cont++;?></td>
                    <td class="show_dados_right"><?=$codBem;?></td>
                    <td class="show_dados"><?=$nomEspecie;?></td>
                    <td class="show_dados"><?=$descricao;?></td>
                    <td class="botao" width="5">
                        <a href='consultaBens.php?<?=Sessao::getId();?>&ctrl=1.2&codBem=<?=$codBem?>'>
                            <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif' title="Consultar" border="0">
                        </a>
                    </td>
                </tr>
<?php
            }
        }
?>
        </table>
<?php

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        $sqlPDF = $sessao->transf;
        $sqlPDF .= " order by $sessao->transf2 DESC";
        //$botoesPDF->imprimeBotoes('../administracao/usuarios/usuario.xml',$sqlPDF,'','');
?>
        <table width="100%" align="center">
        <tr>
            <td align="center"><font size="2">
            <?=$paginacao->mostraLinks();?>
            </font></td>
        </tr>
        </table>
<?php
        $sessao->transf5 = $pagina;

    break;

    // exibe detalhes do BEM

    case 1.3:
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';
        $sSQLs = $sessao->transf2;
        $botoesPDF = new botoesPdfLegado;
        $paginacao = new paginacaoLegada;
        $pagina = $sessao->transf5;
        $paginacao->pegaDados( $sessao->transf2, "10" );
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("b.cod_bem","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
?>
        <table width=100%>
        <tr>
            <td class="alt_dados" colspan="5">Registros de Bens</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%">&nbsp;</td>
            <td class="labelcenter" width="7%">Código</td>
            <td class="labelcenter" width="30%">Espécie</td>
            <td class="labelcenter" width="53%">Descrição</td>
            <td class="labelcenter" width="5%">&nbsp;</td>
        </tr>
<?php
        if ($dbEmp->numeroDeLinhas == 0) {
?>
            <tr>
                <td class="show_dados_center" colspan="5"><b>Nenhum registro encontrado.</b></td>
            </tr>
<?php
        } else {

            $cont = $paginacao->contador();

            while (!$dbEmp->eof()) {
                $codBem  = trim($dbEmp->pegaCampo("cod_bem"));
                $nomEspecie  = trim($dbEmp->pegaCampo("nom_especie"));
                $descricao  = trim($dbEmp->pegaCampo("descricao"));
                $dbEmp->vaiProximo();
?>
                <tr>
                    <td class="labelcenter"><?=$cont++;?></td>
                    <td class="show_dados_right"><?=$codBem;?></td>
                    <td class="show_dados"><?=$nomEspecie;?></td>
                    <td class="show_dados"><?=$descricao;?></td>
                    <td class="botao" width="5">
                        <a href='consultaBens.php?<?=Sessao::getId();?>&ctrl=1.2&codBem=<?=$codBem;?>'>
                            <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif' title="Consultar" border="0">
                        </a>
                    </td>
                </tr>
<?php
            }
        }
?>
        </table>
<?php

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        $sqlPDF = $sessao->transf;
        $sqlPDF .= " order by $sessao->transf2 DESC";
        //$botoesPDF->imprimeBotoes('../administracao/usuarios/usuario.xml',$sqlPDF,'','');
?>
        <table width="100%" align="center">
        <tr>
            <td align="center"><font size="2">
            <?=$paginacao->mostraLinks();?>
            </font></td>
        </tr>
        </table>
<?php
        $sessao->transf5 = $pagina;

    break;

    case 1.2:
     include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';

    $sSQL ="SELECT bem.cod_bem                              ,
                   bem.descricao                            ,
                   bem.num_placa                            ,
                   bem.detalhamento                         ,
                   bem.dt_aquisicao                         ,
                   bem.vl_depreciacao                       ,
                   bem.dt_depreciacao                       ,
                   bem.vl_bem                               ,
                   bem.identificacao                        ,
                   bem.descricao                            ,
                   bem.dt_garantia                          ,
                   bem.numcgm                               ,
                   sw_cgm.nom_cgm                           ,
                   bem_baixado.dt_baixa                     ,
                   bem_baixado.motivo                       ,
                   grupo.cod_grupo                          ,
                   grupo.nom_grupo                          ,
                   natureza.cod_natureza                    ,
                   natureza.nom_natureza                    ,
                   especie.cod_especie                      ,
                   especie.nom_especie                      ,
                   historico_bem.cod_situacao               ,
                   historico_bem.descricao AS desc_situacao ,
                   situacao_bem.nom_situacao                ,
                   orgao.cod_orgao                          ,
                   orgao.nom_orgao                          ,
                   unidade.cod_unidade                      ,
                   unidade.nom_unidade                      ,
                   departamento.cod_departamento            ,
                   departamento.nom_departamento            ,
                   setor.cod_setor                          ,
                   setor.nom_setor                          ,
                   local.cod_local                          ,
                   local.nom_local                          ,
                   local.ano_exercicio
              FROM patrimonio.bem
              LEFT JOIN patrimonio.bem_baixado  ON (bem_baixado.cod_bem  = bem.cod_bem)
              LEFT JOIN sw_cgm                  ON (sw_cgm.numcgm        = bem.numcgm) ,
                   patrimonio.especie                                                           ,
                   patrimonio.grupo                                                             ,
                   patrimonio.natureza                                                          ,
                   patrimonio.historico_bem                                                     ,
                   patrimonio.vw_ultimo_historico                                               ,
                   patrimonio.situacao_bem                                                      ,
                   administracao.orgao                                                          ,
                   administracao.unidade                                                        ,
                   administracao.departamento                                                   ,
                   administracao.setor                                                          ,
                   administracao.local
             WHERE 1 = 1                                                                        ";
    if (isset($codBem)) {
     $sSQL .= "AND bem.cod_bem                      = $codBem";
    }
    $sSQL .="  --AND bem.dt_aquisicao                 = vw_bem_ativo.dt_aquisicao
               AND bem.cod_especie                  = especie.cod_especie
               AND bem.cod_grupo                    = grupo.cod_grupo
               AND bem.cod_natureza                 = natureza.cod_natureza
               AND bem.cod_bem                      = historico_bem.cod_bem
               AND bem.cod_bem                      = vw_ultimo_historico.cod_bem
               AND vw_ultimo_historico.timestamp    = historico_bem.timestamp
               AND especie.cod_grupo                = grupo.cod_grupo
               AND grupo.cod_natureza               = natureza.cod_natureza
               AND historico_bem.cod_bem            = bem.cod_bem
               AND historico_bem.cod_situacao       = situacao_bem.cod_situacao
               AND orgao.cod_orgao                  = historico_bem.cod_orgao
               AND orgao.ano_exercicio              = historico_bem.ano_exercicio
               AND unidade.cod_orgao                = historico_bem.cod_orgao
               AND unidade.cod_unidade              = historico_bem.cod_unidade
               AND unidade.ano_exercicio            = historico_bem.ano_exercicio
               AND departamento.cod_orgao           = historico_bem.cod_orgao
               AND departamento.ano_exercicio       = historico_bem.ano_exercicio
               AND departamento.cod_unidade         = historico_bem.cod_unidade
               AND departamento.cod_departamento    = historico_bem.cod_departamento
               AND setor.cod_unidade                = historico_bem.cod_unidade
               AND setor.ano_exercicio              = historico_bem.ano_exercicio
               AND setor.cod_orgao                  = historico_bem.cod_orgao
               AND setor.cod_departamento           = historico_bem.cod_departamento
               AND setor.cod_setor                  = historico_bem.cod_setor
               AND local.cod_departamento           = historico_bem.cod_departamento
               AND local.cod_orgao                  = historico_bem.cod_orgao
               AND local.ano_exercicio              = historico_bem.ano_exercicio
               AND local.cod_unidade                = historico_bem.cod_unidade
               AND local.cod_setor                  = historico_bem.cod_setor
               AND local.cod_local             = historico_bem.cod_local
             ORDER BY historico_bem.timestamp Desc                                           ";

        if ($numPlaca) {
            $sSQL .=" AND lower(bm.num_placa) like lower('%".$numPlaca."%') ";
        }
        $baixa = false;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $stNatureza      = $dbEmp->pegaCampo("cod_natureza")." - ".$dbEmp->pegaCampo("nom_natureza");
        $stGrupo         = $dbEmp->pegaCampo("cod_grupo")." - ".$dbEmp->pegaCampo("nom_grupo");
        $stEspecie       = $dbEmp->pegaCampo("cod_especie")." - ".$dbEmp->pegaCampo("nom_especie");
        $codBem          = trim( $dbEmp->pegaCampo("cod_bem")       );
        $descricao       = trim( $dbEmp->pegaCampo("descricao")     );

        $detalhamento    = trim( $dbEmp->pegaCampo("detalhamento")  );
        $dtAquisicao     = trim( $dbEmp->pegaCampo("dt_aquisicao")  );
        $dtDepreciacao   = trim( $dbEmp->pegaCampo("dt_depreciacao"));
        $dtGarantia      = trim( $dbEmp->pegaCampo("dt_garantia")   );
        $vlBem           = trim( $dbEmp->pegaCampo("vl_bem")        );
        $vlDepreciacao   = trim( $dbEmp->pegaCampo("vl_depreciacao"));
        $identificacao   = trim( $dbEmp->pegaCampo("identificacao") );
        $nomSituacao     = trim( $dbEmp->pegaCampo("nom_situacao")  );
        $descSituacao    = trim( $dbEmp->pegaCampo("desc_situacao") );
        $fornecedor      = trim( $dbEmp->pegaCampo("numcgm")        );
        $fornecedor_nome = trim( $dbEmp->pegaCampo("nom_cgm")       );
        $numPlaca        = trim( $dbEmp->pegaCampo("num_placa")     );

        if (trim( $dbEmp->pegaCampo("dt_baixa")  ) != null) {
            $dtBaixa         = dataToBr(trim( $dbEmp->pegaCampo("dt_baixa")));
            $motivo    = trim( $dbEmp->pegaCampo("motivo")  );
            $baixa = true;
        }

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

        $dataAquisicao      = dataToBr( $dtAquisicao                     );
        $dataDepreciacao    = dataToBr( $dtDepreciacao                   );
        $dataGarantia       = dataToBr( $dtGarantia                      );
        $valorBemEx         = extenso ( $vlBem                           );
        $valorDepreciacaoEx = extenso ( $vlDepreciacao                   );
        $valorBem           = number_format( $vlBem, 2, ',', '.'         );
        $valorDepreciacao   = number_format( $vlDepreciacao, 2, ',', '.' );

        if ($identificacao == "f") {
            $placaIdentificação = "Não";
        } else {
            $placaIdentificação = "Sim";
        }

        $stOrgao        = $dbEmp->pegaCampo("cod_orgao")." - ".$dbEmp->pegaCampo("nom_orgao");
        $stUnidade      = $dbEmp->pegaCampo("cod_unidade")." - ".$dbEmp->pegaCampo("nom_unidade");
        $stDepartamento = $dbEmp->pegaCampo("cod_departamento")." - ".$dbEmp->pegaCampo("nom_departamento");
        $stSetor        = $dbEmp->pegaCampo("cod_setor")." - ".$dbEmp->pegaCampo("nom_setor");
        $stLocal        = $dbEmp->pegaCampo("cod_local")."/".$dbEmp->pegaCampo("ano_exercicio")." - ".$dbEmp->pegaCampo("nom_local");
        $stLocalizacao = $dbEmp->pegaCampo("cod_orgao")."-".$dbEmp->pegaCampo("cod_unidade")."-".$dbEmp->pegaCampo("cod_departamento")."-".$dbEmp->pegaCampo("cod_setor")."-".$dbEmp->pegaCampo("cod_local")."/".$dbEmp->pegaCampo("ano_exercicio");

        $mascaraSetor   = pegaConfiguracao('mascara_local',2);
        $stLocalizacao = validaMascaraDinamica($mascaraSetor,$stLocalizacao);

?>
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="2">Dados do Bem</td>
        </tr>
        <tr>
            <td class="label">Código do Bem</td><td class="field"><?=$codBem;?></td>
        </tr>

        <tr>
            <td class="alt_dados" colspan="2">Classificação</td>
        </tr>
        <tr>
            <td class="label" width="20%">Natureza</td>
            <td class="field"><?=$stNatureza;?></td>
        </tr>
        <tr>
            <td class="label">Grupo</td>
            <td class="field"><?=$stGrupo;?></td>
        </tr>
        <tr>
            <td class="label">Espécie</td>
            <td class="field"><?=$stEspecie;?></td>
        </tr>
        <tr>
            <td class="alt_dados" colspan="2">Informações Básicas</td>
        </tr>
        <tr>
            <td class="label">Descrição</td>
            <td class="field"><?=$descricao;?></td>
        </tr>
        <tr>
            <td class="label">Detalhamento</td>
            <td class="field"><?=$detalhamento;?></td>
        </tr>
        <tr>
            <td class="label">Fornecedor</td>
            <td class="field"><?=$fornecedor." - ".$fornecedor_nome;?></td>
        </tr>
        <tr>
            <td class="label">Valor do Bem</td>
            <td class="field">R$ <?=$valorBem;?> <font size=1>(<?=$valorBemEx;?> )</font></td>
        </tr>
        <tr>
            <td class="label">Valor de Depreciação</td>
            <td class="field">R$ <?=$valorDepreciacao;?> <font size=1>(<?=$valorDepreciacaoEx;?> )</font></td>
        </tr>
        <tr>
            <td class="label">Data da Depreciação</td>
            <td class="field"><?=$dataDepreciacao;?></td>
        </tr>
        <tr>
            <td class="label">Data da Aquisição</td>
            <td class="field"><?=$dataAquisicao;?></td>
        </tr>
        <tr>
            <td class="label">Vencimento da Garantia</td>
            <td class="field"><?=$dataGarantia;?></td>
        </tr>
        <tr>
            <td class="label">Placa de Identificação</td>
            <td class="field"><?=$placaIdentificação;?></td>
        </tr>
        <tr>
            <td class="label">Número da Placa</td>
            <td class="field"><?=$numPlaca;?></td>
        </tr>
         </table>

        <table width="100%">
<?php
        $sSQL = "SELECT
                    bae.cod_bem, a.nom_atributo, bae.valor_atributo
                FROM
                    patrimonio.bem_atributo_especie as bae,
                    administracao.atributo_dinamico as a
                WHERE
                        bae.cod_atributo = a.cod_atributo
                        AND bae.cod_bem      = ".$codBem;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        if ( !$dbEmp->eof() ) {
?>
        <tr>
            <td class="alt_dados" colspan=2>Atributos</td>
        </tr>
<?php
        }
        while (!$dbEmp->eof()) {
            $codBem  = trim($dbEmp->pegaCampo("cod_bem"));
            $nomAtributo  = trim($dbEmp->pegaCampo("nom_atributo"));
            $valorAtributo  = trim($dbEmp->pegaCampo("valor_atributo"));
            $dbEmp->vaiProximo();
?>

            <tr>
                <td class="label" width="20%"><?=$nomAtributo;?></td>
                <td class="field"><?=$valorAtributo;?></td>
            </tr>
<?php
        }

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
?>
        </table>

        <table width="100%" align="center">
        <tr>
            <td class="alt_dados" colspan="2">Localização</td>
        </tr>
        <tr>
            <td class="label" width="20%">Localização Atual</td>
            <td class="field"><?=$stLocalizacao[1];?></td>
        </tr>
        <tr>
            <td class="label">Orgão</td>
            <td class="field"><?=$stOrgao;?></td>
        </tr>
        <tr>
            <td class="label">Unidade</td>
            <td class="field"><?=$stUnidade;?></td>
        </tr>
        <tr>
            <td class="label">Departamento</td>
            <td class="field"><?=$stDepartamento;?></td>
        </tr>
        <tr>
            <td class="label">Setor</td>
            <td class="field"><?=$stSetor;?></td>
        </tr>
        <tr>
            <td class="label">Local</td>
            <td class="field"><?=$stLocal;?></td>
        </tr>
        <tr>
            <td class="label">Situação</td>
            <td class="field"><?=$nomSituacao;?></td>
        </tr>
        <tr>
            <td class="label">Descrição da Situação</td>
            <td class="field"><?=$descSituacao;?></td>
        </tr>

<?php
    if ($baixa) {
?>
        <tr>
           <td class="alt_dados" colspan="2">Informações sobre Baixa</td>
        </tr>
        <tr>
            <td class="label">Data da Baixa</td>
            <td class="field"><?=$dtBaixa;?></td>
        </tr>
        <tr>
            <td class="label">motivo</td>
            <td class="field"><?=$motivo;?></td>
        </tr>

<?php
    }
?>

        </table>

        <table width="100%">
<?php
        $sSQL = "SELECT
                    bc.cod_empenho, bc.exercicio, c.nom_cgm, bc.nota_fiscal
                FROM
                    patrimonio.bem_comprado as bc
                LEFT OUTER JOIN
                    orcamento.entidade as oe
                ON
                    bc.cod_entidade = oe.cod_entidade
                AND
                    bc.exercicio = oe.exercicio
                LEFT OUTER JOIN
                    sw_cgm as c
                ON
                    c.numcgm = oe.numcgm
                WHERE
                    bc.cod_bem = ".$codBem;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();

        if ( !$dbEmp->eof() ) {
?>
            <tr>
                <td class="alt_dados" colspan="2">Informações Financeiras</td>
            </tr>
<?php
            $stEntidade = trim($dbEmp->pegaCampo("nom_cgm"));
            $stExercicioEmpenho = trim($dbEmp->pegaCampo("exercicio"));
            $stEmpenho = trim($dbEmp->pegaCampo("cod_empenho"))."/".trim($dbEmp->pegaCampo("exercicio"));
            $numNotaFiscal = trim($dbEmp->pegaCampo("nota_fiscal"));
            $dbEmp->vaiProximo();
?>
            <tr>
                <td class="label" width="20%">Entidade</td>
                <td class="field"><?=$stEntidade;?></td>
            </tr>
            <tr>
                <td class="label" width="20%">Exercício do Empenho</td>
                <td class="field"><?=$stExercicioEmpenho;?></td>
            </tr>
            <tr>
                <td class="label" width="20%">Número do Empenho</td>
                <td class="field"><?=$stEmpenho;?></td>
            </tr>
            <tr>
                <td class="label" width="20%">Número da Nota Fiscal</td>
                <td class="field"><?=$numNotaFiscal;?></td>
            </tr>

<?php
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        }
?>
        </table>

        <br>

        <table width="100%" align="center">
        <tr>
            <td align="left">
                <input type="button" value="Voltar" onClick="javascript:document.location.replace('consultaBens.php?<?=Sessao::getId()."&ctrl=1.3";?>');">&nbsp;
            </td>
        </table>
<?php
    break;

}
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
