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
    * Classe para construção da interface HTML de Bens
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Ricardo Lopes de Alencar
    * @author Desenvolvedor Jorge Batista Ribarr
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 28299 $
    $Name$
    $Autor: $
    $Date: 2008-02-29 14:31:59 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.73  2007/07/16 15:20:07  rodrigo
#9628#

Revision 1.72  2007/07/02 13:45:13  rodrigo_sr
Bug#8336#

Revision 1.71  2007/04/26 00:11:42  rodrigo_sr
Bug #8336#

Revision 1.70  2006/11/27 17:22:02  larocca
Bug #7638#

Revision 1.69  2006/07/27 12:20:55  fernando
Bug #6426#

Revision 1.68  2006/07/24 22:25:56  rodrigo
Bug #6426#

Revision 1.67  2006/07/21 11:35:07  fernando
Inclusão do  Ajuda.

Revision 1.66  2006/07/18 14:31:31  fernando
diminuição dos campos na interface(eles agora estão mais próximo da margem direta)

Revision 1.65  2006/07/18 14:01:44  fernando
alteração de hints

Revision 1.64  2006/07/18 13:18:53  fernando
alteração de hint

Revision 1.63  2006/07/13 18:34:58  fernando
Alteração de hints

Revision 1.62  2006/07/12 19:15:26  gelson
Correção dos hints

Revision 1.61  2006/07/06 18:43:58  fernando
incluído o campo de exercício do empenho no incluir e alterar bem.

Revision 1.60  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.59  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once (CAM_GP."javaScript/ifuncoesJsGP.js");
class interfaceBens
{
    public $acaoMenu;  //código da acao do menu - jorge

/**************************************************************************
 Gera o Combo com os tipos de situação do patrimômio
/**************************************************************************/
    public function comboSituacao($nome="situacao",$default="",$valorNulo=1)
    {
        $combo = "";
        $combo .= "<select name='".$nome."' id='situacao' style='width: 200px;
            'onchange='javascript:preencheCampo(this, document.frm.codTxtSituacao
        );'>\n";
            if($default=="")
                $selected = "selected";
        if($valorNulo)
            $combo .= "<option value='xxx' ".$selected.">Selecione a situação do bem</option>\n";

        $sql = "Select cod_situacao, nom_situacao
                From patrimonio.situacao_bem Order by nom_situacao";
        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->fechaBD();
        $dataBase->vaiPrimeiro();

        while (!$dataBase->eof()) {
            $codSituacao = trim($dataBase->pegaCampo("cod_situacao"));
            $nomSituacao = trim($dataBase->pegaCampo("nom_situacao"));
            $selected = "";
                if($codSituacao==$default)
                    $selected = "selected";
            $dataBase->vaiProximo();
            $combo .=
                "<option value='".$codSituacao."' ".$selected.">".$nomSituacao."</option>\n";
        }

        $dataBase->limpaSelecao();
        $combo .= "</select>";

        return $combo;
    }//Fim function comboSituacao

/**************************************************************************
 Gera os campos texto para os atributos de uma espécie
/**************************************************************************/
    public function geraAtributos($especie,$grupo,$natureza,$codBem="",$vetAtributo="",$stAcao="")
    {
        $sql = "Select A.cod_atributo, A.nom_atributo, A.tipo, A.valor_padrao
                From administracao.atributo_dinamico as A, patrimonio.especie_atributo as E
                Where A.cod_atributo = E.cod_atributo
                And E.cod_especie = '".$especie."'
                And E.cod_grupo = '".$grupo."'
                And E.cod_natureza = '".$natureza."'";

        echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
        $campoTexto = "";
        while (!$dataBase->eof()) {
            $codAtributo  = addslashes(trim($dataBase->pegaCampo("cod_atributo")));
            $nomAtributo  = addslashes(trim($dataBase->pegaCampo("nom_atributo")));
            $tipoAtributo = addslashes(trim($dataBase->pegaCampo("tipo")));
            $valorPadrao  = addslashes(trim($dataBase->pegaCampo("valor_padrao")));
            $value = "";
            if ($stAcao == 'alterar') {
                $sql = "SELECT
                            B.valor_atributo
                        FROM
                            patrimonio.bem_atributo_especie as B
                        WHERE
                            B.cod_atributo = '".$codAtributo."'
                        AND B.cod_bem      = '".$codBem."'
                        AND B.cod_grupo    = '".$grupo."'
                        AND B.cod_natureza = '".$natureza."'
                        AND B.cod_especie  = '".$especie."'";
                $dataBaseAtrib = new dataBaseLegado;
                $dataBaseAtrib->abreBD();
                $dataBaseAtrib->abreSelecao($sql);
                $dataBaseAtrib->vaiPrimeiro();
                if ($tipoAtributo != "l") {
                    $valorPadrao = trim($dataBaseAtrib->pegaCampo("valor_atributo"));
                } else {
                    $valorSelect = trim($dataBaseAtrib->pegaCampo("valor_atributo"));
                }

                $dataBaseAtrib->limpaSelecao();
            }

            $campoTexto .= "<table cellspacing='1' cellpadding='2' width='100%'>";

            if (is_array($vetAtributo)) {
                $value = $vetAtributo[$codAtributo];
            }

            if ($tipoAtributo == "t") {
                $campoTexto .=  "<tr>";
                $campoTexto .=  " <td class=label width=20%>*";
                $campoTexto .= $nomAtributo;
                $campoTexto .= "  </td>";
                $campoTexto .= "  <td class=field>";
                $campoTexto .= "      <input type='text' name='atributos[".$codAtributo."]' id='atributos".$codAtributo."'  value='$valorPadrao'>";
                $campoTexto .= "  </td></tr>";
            }

            if ($tipoAtributo == "n") {
                $campoTexto .= "<tr>";
                $campoTexto .= "  <td class=label width=20%>*";
                $campoTexto .= $nomAtributo;
                $campoTexto .= "  </td>";
                $campoTexto .= "  <td class=field>";
                $campoTexto .= "      <input type='text' name='atributos[".$codAtributo."]' id='atributos".$codAtributo."' value='".$valorPadrao."' onKeyPress=return(isValido(this,event,'0123456789'))>";
                $campoTexto .= "  </td></tr>";
            }

            if ($tipoAtributo == "l") {
                $campoTexto .= "<tr>";
                $campoTexto .= "  <td class=label width=20%>*";
                $campoTexto .= $nomAtributo;
                $campoTexto .= "  </td>";
                $campoTexto .= "  <td class=field>";
                $lista = explode("\n", $valorPadrao);
                $count = 0;
                while (list($key, $val) = each($lista)) {
                    $val = trim($val);
                    $arrayAtributos[$count] = $val;
                    $count++;

                }
                sort($lista);
                reset($lista);

                $selected = "";
                $campoTexto .= "  <select name='atributos[".$codAtributo."]' id='atributos".$codAtributo."'>";
                while (list($key, $val) = each($lista)) {
                    $val = trim($val);
                    if ($valorSelect == $val) {
                        $selected = "selected";
                    }
                    $campoTexto .= "      <option value='".$val."' ".$selected.">".$val."</option>";
                    $selected = "";
                }
                $campoTexto .="  </select>";
                $campoTexto .= "  </td></tr>";
            }
            $dataBase->vaiProximo();
        }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $campoTexto;
    }//Fim function geraAtributos

/**************************************************************************
 Monta o formulário para inclusão ou alteração dos dados de um bem
 Entra com o vetor contendo os dados do formulário, a página para qual
 o formulário será enviado e uma variável auxiliar
/**************************************************************************/
    public function formCadastroBens($vet="",$action="",$ctrl=0,$reload=0,$formAcao="")
    {
        // buscara mascara do setor
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
        $mascaraSetor = pegaConfiguracao("mascara_local");

        //Carrega os campos do vetor como variáveis, cada qual com seu respectivo valor
        if (is_array($vet)) {
            foreach ($vet as $chave=>$valor) {
                $$chave = $valor;
            }
            $valorBem = preg_replace( "/,/",".",$valorBem);
            $valorDepreciacao = preg_replace( "/,/",".",$valorDepreciacao);

            $valorBem = number_format($valorBem, 2, ',', '');
            $valorDepreciacao = number_format($valorDepreciacao, 2, ',', '');

            if(strlen($exercicio)==0)
                $exercicio = pegaConfiguracao("ano_exercicio");

            if(strlen($exercicioEmpenho)==0)
                $exercicioEmpenho = pegaConfiguracao("ano_exercicio");

            if ($formAcao=='alterar' and $reload==0) {
                if(strlen($dataAquisicao)>2)
                    $dataAquisicao = dataToBr($dataAquisicao);
                if(strlen($dataDepreciacao)>2)
                    $dataDepreciacao = dataToBr($dataDepreciacao);
                if(strlen($dataGarantia)>2)
                    $dataGarantia = dataToBr($dataGarantia);
            }
        }

        // operacoes no frame oculto
        switch ($ctrl_frm) {

            // busca o nome do fornecedor a partir do codigo informado
            case 1:
                if ($formAcao=='alterar') {
                    $js = "f.controle.value = 3;";
                } else {
                    $js = "f.controle.value = 0;";
                }

                if (!$fornecedor) {
                    $fornecedor = "0";
                }

                // busca nome do fornecedor atraves do cod_fornecedor informado
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

                executaFrameOculto($js);
                exit();
            break;

            // preenche os combos do Local
            case 2:
      include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosCALOLegado.inc.php';
                exit();
            break;

            // preenche Natureza, Grupo e Especie
            case 3:

     include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosNGE.inc.php';
                exit();
            break;

            case 4:

                if ($codEspecie == "xxx") {
                    $js .= 'd.getElementById("sAtributos").innerHTML = "";';
                } else {
                    // monta HTML para exibicao dos atributos
                    $atributosHTML = $this->geraAtributos($codEspecie,$codGrupo,$codNatureza,$atributos,$formAcao);
                    $js .= 'd.getElementById("sAtributos").innerHTML = "'.$atributosHTML.'";';
                }
                executaFrameOculto($js);
                exit();
            break;

            case 5:
                $js .= 'd.getElementById("sFornecedor").innerHTML = "&nbsp;";';
                executaFrameOculto($js);

            break;
            case 6:
                     $sSQL = "SELECT
                        max(OE.exercicio) as exercicio
                    FROM
                        sw_cgm as C,
                        orcamento.entidade AS OE
                    WHERE
                            C.numcgm = OE.numcgm
                        AND OE.cod_entidade = $codEntidade
                    GROUP BY
                         OE.cod_entidade
                        ,C.nom_cgm
                    ORDER BY
                        C.nom_cgm ";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                    $exercicioEmpenho = $dbEmp->pegaCampo('exercicio');

                     $js = "f.exercicioEmpenho.value. = '".$exercicioEmpenho."';";

               executaFrameOculto($js);
               exit();
            break;
       }
// encerra operacoes no frame oculto

?>
        <script type="text/javascript">
<?php
            if (isset($codBem)) {
?>
                function excluirBem()
                {
                    var bem = <?=$codBem;?>;
                    var objeto = "<?=str_replace('"', '\"', str_replace(chr(13).chr(10)," ",$descricao));?>";

                    alertaQuestao('../patrimonio/bens/excluiBem.php','excluir',bem,objeto,'sn_excluir','<?=Sessao::getId()?>');
                }
<?php
            }
?>

            function mudaAtributo()
            {
                document.frm.controle.value = "<?=$ctrl;?>";
                document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=<?=$ctrl;?>";
                document.frm.submit();
            }

            function valorMaximo(campo, limite)
            {
                if (campo.value.length > limite) // se estiver maior que o tamanho estabelecido, reduza-o
                    campo.value = campo.value.substring(0, limite);
            }

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;

<?php
                if ($especie>0 and $grupo>0 and $natureza>0) {
                    echo $this->geraScriptAtributos($especie,$grupo,$natureza,$atributos);
                }
?>

                campo = document.frm.codNatureza.value;
                    if (campo=='xxx') {
                        mensagem += "@O campo natureza é obrigatório.";
                        erro = true;
                    }

                campo = document.frm.codGrupo.value;
                    if (campo=='xxx') {
                        mensagem += "@O campo grupo é obrigatório.";
                        erro = true;
                    }

                campo = document.frm.codEspecie.value;
                    if (campo=='xxx') {
                        mensagem += "@O campo espécie é obrigatório.";
                        erro = true;
                    }

                campo = trim(document.frm.descricao.value).length;
                    if (campo==0) {
                        mensagem += "@O campo descrição é obrigatório.";
                        erro = true;
                    }

                campo = document.frm.fornecedor.value.length;
                    if (campo==0) {
                        mensagem += "@O campo fornecedor é obrigatório.";
                        erro = true;
                    }

                campo = document.frm.fornecedor.value;
                    if (isNaN(campo)) {
                        mensagem += "@O campo Fornecedor só aceita Números.";
                        erro = true;
                    }

                campo = document.frm.valorBem.value.length;
                    if (campo==0) {
                        mensagem += "@O campo valor bem é obrigatório.";
                        erro = true;
                    }

                campo = document.frm.valorDepreciacao.value.length;
                    if (campo==0) {
                        document.frm.valorDepreciacao.value = 0;
                    }

                campo = document.frm.dataAquisicao.value.length;
                    if (campo==0) {
                        mensagem += "@O campo data da aquisição é obrigatório.";
                        erro = true;
                    }
                var dataAtual = "<?=date("Ymd")?>";
                dataAtual = parseInt(dataAtual);
                campo = document.frm.dataAquisicao.value;
                campo = campo.split("/");
                campo = campo[2]+campo[1]+campo[0];
                if (parseInt(campo) > dataAtual) {
                    mensagem += "@O Campo Data da Aquisição deve ser menor ou igual a data atual.";
                    erro = true;
                }
<?php
if ($formAcao != 'incluir_lote') {
?>
                 campo = document.frm.identificacao[1].checked;
                 campoaux = document.frm.numPlaca.value.length;
                    if ((campo==true) && (campoaux==0)) {
                        mensagem += "@O campo número da placa é obrigatório.";
                        erro = true;
                    }
<?php
}
?>
                 campo = document.frm.exercicioEmpenho.value.length;
                    if (campo==0) {
                        mensagem += "@O campo exercício é obrigatório.";
                        erro = true;
                    }
                campo = document.frm.codEntidade.value.length;
                    if (campo==0) {
                        mensagem += "@O campo entidade é obrigatório.";
                        erro = true;
                    }

<?php
            if ($formAcao=='incluir_lote') {
?>
                campo = document.frm.iQtde.value.length;
                    if (campo==0) {
                        mensagem += "@O campo quantidade de bens a incluir é obrigatório.";
                        erro = true;
                    }
<?php
            }
?>
                campo = document.frm.codMasSetor.value;
                    if (campo=='') {
                        mensagem += "@O campo local é obrigatório.";
                        erro = true;
                    }

                campo = document.frm.situacao.value;
                    if (campo=='xxx') {
                        mensagem += "@O campo situação é obrigatório.";
                        erro = true;
                    }
//                campo = document.frm.exercicioEmpenho.value;
//                campoaux = document.frm.dataAquisicao.value;
//                   if (campo > campoaux.substring(campoaux.length-4, campoaux.length)) {
//                        mensagem += "@O ano do exercicio é diferente do ano da aquisição.";
//                        mensagem += "@O ano do exercício não pode ser superior ao ano da data de aquisição.";
//                        erro = true;
//                   }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);

            }// Fim da function Valida

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
<?php
                    if ($formAcao=='alterar') {
?>
                        document.frm.controle.value = '2';
<?php
                    } else {
?>
                        document.frm.controle.value = '1';
<?php
                    }
?>
                    //document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=1";
                    document.frm.ctrl_frm.value = '0';
                    document.frm.target = "oculto";
                    document.frm.submit();
                }
            }
            function Cancela()
            {
                 mudaTelaPrincipal('<?=$action;?>?<?=Sessao::getId();?>&controle=0&ctrl_frm=2&pagina="<?=$sessao->filtro['pagina'];?>');
            }

            // funcao que busca Conta de Débito no frame oculto
            function busca_fornecedor(cod)
            {
                document.frm.controle.value = "<?=$ctrl;?>";
                document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=<?=$ctrl;?>";

                var f = document.frm;
                f.target = 'oculto';
                f.ctrl_frm.value = cod;
                f.submit();
            }

            function busca_exercicio()
            {
                document.frm.controle.value = "<?=$ctrl;?>";
                document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=<?=$ctrl;?>";

                var f = document.frm;
                f.target = 'oculto';
                f.ctrl_frm.value = 6;
                f.submit();
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

            // preenche os combos do local (Orgao, Unidade, Departamento, Setor e Local)
            function preencheLocal(variavel, valor)
            {
                document.frm.target = "oculto";
<?php
                if ($formAcao=='alterar') {
?>
                    document.frm.controle.value = "3";
<?php
                }
?>
                document.frm.ctrl_frm.value = "2";
                document.frm.variavel.value = variavel;
                document.frm.valor2.value = escape(valor);
                document.frm.submit();
            }

            // preenche os combos de Natureza, Grupo e Especie
            function preencheNGE(variavel, valor)
            {
                document.frm.target = "oculto";
<?php
                if ($formAcao=='alterar') {
?>
                    document.frm.controle.value = '3';
<?php
                }
?>
                document.frm.ctrl_frm.value = "3";
                document.frm.variavel.value = variavel;
                document.frm.valor2.value = valor;
                document.frm.submit();
            }

            // busca os atributos de acordo com a Natureza/Grupo/Especie selecionados
            function listaAtributos(especie)
            {
<?php
                if ($formAcao=='alterar') {
?>
                    document.frm.controle.value = '3';
<?php
                }
?>
                if (especie == "xxx") {
                    //document.frm.codTxtEspecie.value = "";
                    document.frm.target = "oculto";
                    document.frm.ctrl_frm.value = "4";
                    document.frm.submit();

                } else {
                    //document.frm.Ok.disable = false;
                    //document.frm.codTxtEspecie.value = especie;
                    document.frm.target = "oculto";
                    document.frm.ctrl_frm.value = "4";
                    document.frm.submit();
                }
          }
        </script>

<!--FORMULARIO PARA INSERCAO / ALTERACAO DE BENS-->
        <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId();?>' onReset="Limpar();">

            <input type="hidden" name="controle" value=''>
            <input type="hidden" name="reload" value='<?=$reload+1;?>'>
            <input type="hidden" name="ctrl_frm" value=''>

<?php // os hiddens abaixo sao usados para a manipulacao dos filtros CALO e NGE. ?>
            <input type="hidden" name="variavel" value=''>
            <input type="hidden" name="valor2" value=''>
            <input type="hidden" name="codBem" value="<?=$codBem;?>" >

        <table width="100%">
<?php
        if (isset($codBem)) {
?>
        <tr><td class="alt_dados" colspan='2' heigth='5'>Dados do Bem</td></tr>
            <tr>
                <td class="label" >Código do Bem</td><td class="field"><?=$codBem?></td>
            </tr>
<?php
        }
        $descricao = str_replace('\'','&#39;',$descricao);
?>

        <tr><td class="alt_dados" colspan='2' heigth='5'>Classificação</td></tr>

        <tr>
            <td class="label" width="20%" title="Selecione a natureza do bem.">*Natureza</td>
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
            <td class="label" width="20%"  title="Selecione o grupo do bem.">*Grupo</td>
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
            <td class="label" width="20%" title="Selecione a espécie do bem.">*Espécie</td>
            <td class="field">
            <!--
                <input type="text" name="codTxtEspecie"
                    value="<?=$codEspecie != "xxx" ? $codEspecie : "";?>" size="10" maxlength="10"
                    onChange="javascript: verificaCombo(this, document.frm.codEspecie); listaAtributos(this.value);"
                    onKeyPress="return(isValido(this, event, '0123456789'));">
            -->
                <select name="codEspecie" onChange="javascript: listaAtributos(this.value);" style="width:300px" >
                    <option value="xxx" SELECTED>Selecione</option>
                </select>

                <input type="hidden" name="nomEspecie" value="">
            </td>
        </tr>

        <tr><td class="alt_dados" colspan='2' heigth='5'>Informações Básicas</td></tr>

        <tr>
            <td class="label"  title="Informe a descrição do bem.">*Descrição</td>
            <td class="field">
                <input type="text" name="descricao"  value='<?=$descricao;?>'  size="80" maxlength="60">
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o detalhamento do bem.">Detalhamento</td>
            <td class="field">
                <textarea name='detalhamento' cols='40'  rows='4'><?=$detalhamento;?></textarea>
            </td>
        </tr>
        <tr>
            <td class="label"  title="Informe o fornecedor do bem.">*Fornecedor</td>
            <td class="field">

                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                <tr>
                <td align="left" width="11%" valign="top">
                    <input type='text' id='fornecedor' name='fornecedor'
                    value='<?=$fornecedor;?>' size='10' maxlength='10' onBlur="busca_fornecedor(1);"
                    onKeyPress="return(isValido(this, event, '0123456789'))">
                    <input type="hidden" name="Hdnfornecedor" value="<?=$sfornecedor;?>">
                    <input type="hidden" name="sFornecedor" value="<?=$sfornecedor2;?>">
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
            <td class="label"  title="Informe o valor do bem.">*Valor do Bem</td>
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
<?php
//        if ($formAcao=='alterar') {
            geraCampoData2("Data da Depreciação", "dataDepreciacao", $dataDepreciacao, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data da depreciação do bem","Buscar data da depreciação" );
//        }

        geraCampoData2("*Data da Aquisição", "dataAquisicao", $dataAquisicao, false, "onChange=\"JavaScript: if (!validaGarantia(document.frm.dataGarantia.value ,this.value)){alertaAviso('@Data da Garantia precisa ser posterior à data deAquisição!','form','erro','Sessao::getId()');this.value='';};\"  onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if(!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data da aquisição","Buscar data da aquisição" );

        geraCampoData2("Vencimento da Garantia", "dataGarantia", $dataGarantia, false, "onChange=\"JavaScript: if (!validaGarantia(this.value,document.frm.dataAquisicao.value)){alertaAviso('@Data da Garantia precisa ser posterior à data da Aquisição!','form','erro','Sessao::getId()');this.value='';};\"   onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if(!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data do vencimento da garantia do bem","Buscar data do vencimento" );?>

<?php
   if ($formAcao!='incluir_lote') {
?>

        <tr>
            <td class="label" title="Informe se o bem possui placa de identificação." >Placa de Identificação</td>
            <td class="field">
                <input type="radio" name="identificacao" value="N" onClick="document.frm.numPlaca.value='';document.frm.numPlaca.disabled = true;"
<?php
                if ($identificacao == 'f' or $identificacao == 'N') {
                    echo " checked";
                }
?>
                >Não

                <input type="radio" name="identificacao" value="S" onClick="document.frm.numPlaca.disabled = false;"
<?php
                if ($identificacao == 't' or $identificacao == 'S' or $identificacao == '') {
                    echo " checked";
                }
?>
                >Sim
            </td>
        </tr>

        <tr>
            <td class="label" title = "Informe o número da placa do bem.">*Número da Placa</td>
            <td class="field">
                <?$numPlaca = str_replace('"','&#034;',$numPlaca)?>
                <input type="text" name="numPlaca" value="<?=$numPlaca;?>" size='20' maxlength=20>
            </td>
        </tr>

<?php
    }
?>
<?php
/* comentado o campo código entidade
        <tr>
            <td class="label" title = "Informe o código da Entidade.">Código da Entidade</td>
            <td class="field">
                <input type="text" name="codEntidade" value="<?=$codEntidade;?>" size='05' maxlength=05>
            </td>
        </tr>
*/
?>
<?php
        // exibe Atributos de acordo com Especie, Grupo e Natureza selecionados
        if ($formAcao=='alterar') {
            $atributosHTML = $this->geraAtributos($codEspecie,$codGrupo,$codNatureza,$codBem,$atributos,$formAcao);
            $js .= 'd.getElementById("sAtributos").innerHTML = "'.$atributosHTML.'";';
            executaFrameOculto($js);
        }
?>

        <tr>
            <td class="alt_dados" colspan='2' heigth='5'>Atributos</td>
        </tr>

        <tr>
            <td colspan="2">
                <div id="sAtributos"> </script></div>
            </td>
        </tr>

        <tr>
            <td class="alt_dados" colspan='2' heigth='5'>Informações Financeiras</td></tr>
        <tr>
            <td class="label" title="Selecione a entidade do bem.">*Entidade</td>
            <td class="field">                 <select name="codEntidade" onChange="busca_exercicio();">
                   <option value='' SELECTED>Selecione</option>
<?php
                     $sSQL = "SELECT
                        OE.cod_entidade,
                        C.nom_cgm
                    FROM
                        sw_cgm as C,
                        orcamento.entidade AS OE
                    WHERE
                        C.numcgm = OE.numcgm
                    GROUP BY
                         OE.cod_entidade
                        ,C.nom_cgm
                    ORDER BY
                        C.nom_cgm ";

                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                            // monta combo com Entidade
                     while (!$dbEmp->eof()) {
                        $arEntidade[$dbEmp->pegaCampo('cod_entidade')]['nom_cgm'] = $dbEmp->pegaCampo('nom_cgm');
                        $dbEmp->vaiProximo();
                     }
                     $comboEntidade = "";
                     foreach ($arEntidade as $key => $entidade) {
                        $comboEntidade .= ' <option value='.$key;
                        if ($key == $codEntidade) {
                             $comboEntidade .= " SELECTED";
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
                <input type="text" name="codEmpenho" value="<?=$codEmpenho;?>" size='10' maxlength='9' onKeyUp="return autoTab(this, 9, event);" onKeyPress="return(isValido(this, event, '0123456789'));">
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o número da nota fiscal do bem.">Número da Nota Fiscal</td>
            <td class="field">
                <input type="text" name="numNotaFiscal" value="<?=$numNotaFiscal;?>" size='21' maxlength='20' >
            </td>
        </tr>

        <tr>
            <td class="alt_dados" colspan='2' heigth='5'>Histórico</td>
        </tr>

        <tr>
            <td class="label"  title="Informe a localização do bem." width="20%">*Localização</td>
            <td class="field">
                <input type="text" name="codMasSetor" value="<?=$codMasSetor?>" size="<?=strlen($mascaraSetor);?>" maxlength="<?=strlen($mascaraSetor);?>"
                    onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraSetor?>', this, event);"
                    onChange="JavaScript: preencheLocal( 'codMasSetor', this.value )">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o orgão do bem." width="20%">*Orgão</td><td class='field'>
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
                        $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                        $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                        $anoE       = trim($dbEmp->pegaCampo("ano_exercicio"));
                        $chave = $codOrgaof."-".$anoE;
                        $dbEmp->vaiProximo();
                        $comboCodOrgao .= "<option value='".$chave."'";
                        if (isset($codOrgao)) {
                            if ($chave == $codOrgao) {
                                $comboCodOrgao .= " SELECTED";
                                $nomOrgao = $nomOrgaof;
                            }
                        }
                        $comboCodOrgao .= ">".$nomOrgaof." - ".$anoE."</option>\n";
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
            <td class="label"  title="Selecione a Unidade do bem." width="20%">*Unidade</td>
            <td class=field>
                <select name=codUnidade onChange="javascript: preencheLocal('codUnidade', this.value);" style="width:300px">
                    <option value=xxx SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomUnidade" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o departamento do bem." width="20%">*Departamento</td>
            <td class="field">
                <select name="codDepartamento" onChange="javascript: preencheLocal('codDepartamento', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomDepartamento" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o setor do bem." width="20%">*Setor</td>
            <td class="field">
                <select name="codSetor" onChange="javascript: preencheLocal('codSetor', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomSetor" value="">
                <input type="hidden" name="anoExercicioSetor" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o local do bem." width="20%">*Local</td>
            <td class="field">
                <select name="codLocal" onChange="javascript: preencheLocal('codLocal', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomLocal" value="">
                <input type="hidden" name="anoExercicioLocal" value="">
            </td>
        </tr>
        <tr>
            <td class="label" title="Selecione a situação do bem." >*Situação</td>
            <td class="field">
                <input type="text" name="codTxtSituacao" value="<?=$situacao != "xxx" ? $situacao : "";?>" size="10" maxlength="10"
                onChange="javascript: preencheCampo(this, document.frm.situacao);"
                onKeyPress="return(isValido(this, event, '0123456789'));">

                <?php echo $this->comboSituacao("situacao",$situacao); ?>
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe a descrição da situação do bem.">Descrição da Situação</td>
            <td class="field">
                <input type="text"  name="descSituacao" value="<?=$descSituacao;?>" size="80" maxlength="60" >
            </td>
        </tr>

<?php
        // inclusao de bem em lote
        if ($formAcao=='incluir_lote') {
?>
            <tr><td class="alt_dados" colspan='2' heigth='5'>Bens em Lote</td></tr>
            <tr>
                <td class="label" title="Informe a quantidade de bens a incluir." >*Quantidade de Bens a Incluir</td>
                <td class="field">
                    <input type="text" name="iQtde" value="<?=$iQtde;?>" size='10' maxlength='10'
                    onKeyUp="return autoTab(this, 3, event);">
                </td>
            </tr>
<?php
        }
?>

        <tr>
            <td colspan='2' class='field'>
<?php
        if ($formAcao=='alterar') {
?>
               <?php geraBotaoOk(1,0,1,1); ?>
<?php
       } else {
?>
              <?php geraBotaoOk(); ?>
<?php
       }
?>

            </td>
        </tr>

        </table>

        </form>
<?php
        // se o formulario estiver sendo aberto para a alteracao de um bem
        // preenche combos/campos de:
        // - Natureza/Grupo/Especie
        // - Local
        // - Fornecedor

        if ($formAcao=='alterar') {

            // preenche os combos de Natureza/Grupo/Especie
            $valor = $codNatureza."-".$codGrupo."-".$codEspecie;
            $variavel = "NatGrpEsp";
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosNGE.inc.php';

            // preenche os combos do Local
            $valor = $codMasSetor;
            $variavel = "codMasSetor";
 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosCALOLegado.inc.php';

?>
            <script>busca_fornecedor(1);</script>
<?php
        }
    }
//FIM FORMULARIO PARA INSERCAO / ALTERACAO DE BENS

/**************************************************************************
 Mostra os tipos de transferências possíveis
/**************************************************************************/
    public function listaTipoTransferencia()
    {
?>
        <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId()?>&controle=0'>

        <table width="100%">

        <tr>
            <td class="alt_dados" colspan="2">Selecione o Tipo de Transferência</td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Selecione o tipo de transferência do bem.">*Tipo de Transferência</td>
            <td class="field">
                <select name='controle' style='width: 300px;' onChange='document.frm.submit();'>
                    <option value='0'>Selecione o Tipo de Transferência</option>
                    <option value='1'>Transferência de um Único Bem</option>
                    <option value='4'>Transferência em Lote</option>
                </select>
            </td>
        </tr>

        </table>

        </form>
<?php
    }//Fim da function listaTipoTransferencia

/**************************************************************************
 Formulário para transferir bens de local em lote
/**************************************************************************/
    public function formTransfereBensLote($local,$action="",$ctrl=0)
    {
        $vetLocal = preg_split( "/[^a-zA-Z0-9]/", $local);
            $codOrgao = $vetLocal[0];
            $codUnidade = $vetLocal[1];
            $codDpto = $vetLocal[2];
            $codSetor = $vetLocal[3];
            $codLocal = $vetLocal[4];
            $anoExLocal = $vetLocal[5];

            $exercicio = pegaConfiguracao("ano_exercicio");

        $sql = "SELECT
                    B.cod_bem, B.descricao, H.timestamp, H.cod_situacao
                FROM
                    patrimonio.vw_bem_ativo as B, patrimonio.historico_bem as H, patrimonio.vw_ultimo_historico as U
                WHERE
                    U.timestamp = H.timestamp
                    And U.cod_bem = H.cod_bem
                    And H.cod_bem = B.cod_bem
                    And H.cod_local = '".$codLocal."'
                    And H.cod_setor = '".$codSetor."'
                    And H.cod_departamento = '".$codDpto."'
                    And H.cod_unidade = '".$codUnidade."'
                    And H.cod_orgao = '".$codOrgao."'
                    And H.ano_exercicio = '".$anoExLocal."'
                    ORDER by B.cod_bem ";
        //echo $sql;
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        if($conn->numeroDeLinhas==0)

            return false;
?>
        <script type="text/javascript">
            function valorMaximo(campo, limite)
            {
                if (campo.value.length > limite) // se estiver maior que o tamanho estabelecido, reduza-o
                    campo.value = campo.value.substring(0, limite);
            }

            var checkflag = "false";
            function check(field)
            {
                if (checkflag == "false") {
                    for (i = 0; i < field.length; i++) {
                        field[i].checked = true;
                    }
                checkflag = "true";

                return "Limpar Todos";
                } else {
                    for (i = 0; i < field.length; i++) {
                        field[i].checked = false;
                        }
                checkflag = "false";

                return "Selecionar todos"; }
            }

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;

                campo = document.frm.local.value.length;
                        if (campo==0) {
                            mensagem += "@O campo local é obrigatório.";
                            erro = true;
                        }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);

            }// Fim da function Valida

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
                    document.frm.submit();
                }
            }
        </script>

        <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId()?>'>
            <input type="hidden" name="controle" value='<?=$ctrl+1;?>'>

        <table width="100%">
        <tr>
            <td class='alt_dados' width='1%'>&nbsp;</td>
            <td class='alt_dados' width='9%'>Código</td>
            <td class='alt_dados' width='50%'>Descrição</td>
            <td class='alt_dados' width='40%'>Situação</td>
        </tr>
<?php
        while (!$conn->eof()) {
            $codBem = $conn->pegaCampo("cod_bem");
            $descricao = $conn->pegaCampo("descricao");
            $situacao = $conn->pegaCampo("cod_situacao");
?>
            <tr>
                <td class="fieldright" width="1%">
                    <input type="checkbox" name="codBem[<?=$codBem;?>]" id='codBem' value="<?=$codBem;?>">
                </td>
                <td class="field" width="9%"><?=$codBem;?></td>
                <td class='field' width='50%'><?=$descricao;?></td>
                <td class="fieldcenter" >
                    <?php echo $this->comboSituacao("situacao[".$codBem."]",$situacao,false); ?>
                </td>
            </tr>
<?php
            $conn->vaiProximo();
        }//Fim While

        //Fecha conexão com o banco de dados
        $conn->limpaSelecao();
?>
        <tr>
            <td colspan='4' class='field'>
                &nbsp;<input type="button" value="Selecionar todos"
                onClick="this.value=check(this.form.codBem)">
            </td>
        </tr>
        </table>

        <table width="100%">
        <tr>
            <td class='alt_dados' colspan="2">Destino</td>
        </tr>
        <tr>
            <td class="label"  title="<?=$title;?>" width="20%">*Localização</td>
            <td class="field">
                <input type="text" name="codMasSetor" value="<?=$codMasSetor?>" size="<?=strlen($mascaraSetor);?>" maxlength="<?=strlen($mascaraSetor);?>"
                    onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraSetor?>', this, event);"
                    onChange="JavaScript: preencheLocal( 'codMasSetor', this.value )">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o orgão do bem." width="20%">*Orgão</td>
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
                        $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                        $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                        $chave = $codOrgaof;
                        $dbEmp->vaiProximo();
                        $comboCodOrgao .= "<option value='".$chave."'";
                        if (isset($codOrgao)) {
                            if ($chave == $codOrgao) {
                                $comboCodOrgao .= " SELECTED";
                                $nomOrgao = $nomOrgaof;
                            }
                        }
                        $comboCodOrgao .= ">".$nomOrgaof."</option>\n";
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
            <td class="label"  title="Selecione a Unidade do bem." width="20%">*Unidade</td>
            <td class=field>
                <select name=codUnidade onChange="javascript: preencheLocal('codUnidade', this.value);" style="width:300px">
                    <option value=xxx SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomUnidade" value="">
            </td>
        </tr>
        <tr>
             <td class="label"  title="Selecione o departamento do bem." width="20%">*Departamento</td>
            <td class="field">
                <select name="codDepartamento" onChange="javascript: preencheLocal('codDepartamento', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomDepartamento" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o setor do bem." width="20%">*Setor</td>
            <td class="field">
                <select name="codSetor" onChange="javascript: preencheLocal('codSetor', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomSetor" value="">
                <input type="hidden" name="anoExercicioSetor" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o local do bem." width="20%">*Local</td>
            <td class="field">
                <select name="codLocal" onChange="javascript: preencheLocal('codLocal', this.value);" style="width:300px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomLocal" value="">
                <input type="hidden" name="anoExercicioLocal" value="">
            </td>
        </tr>
        <tr>
            <td class="label" >Descrição da Situação</td>
            <td class="field">
                <textarea name='descSituacao' cols='40' rows='4'
                onKeyDown="valorMaximo(this.form.descSituacao,100);"
                onKeyUp="valorMaximo(this.form.descSituacao,100);"
                ></textarea>
            </td>
        </tr>
        <tr>
            <td colspan='2' class='field'>
                <?php geraBotaoOk(); ?>
            </td>
        </tr>
        </table>

        </form>

<?php

        return true;
    }//Fim da function formTransfereBensLote

/**************************************************************************
 Monta um formulário para inserir o código do bem e enviar
 para página de exclusão
/**************************************************************************/
    public function formBaixaBem($vet="",$action,$ctrl,$tipoBaixa="")
    {
        //Carrega os campos do vetor como variáveis, cada qual com seu respectivo valor
        if (is_array($vet)) {
            foreach ($vet as $chave=>$valor) {
                $$chave = $valor;
            }
        }
        // operacoes no frame oculto
        switch ($ctrl_frm) {

            case 1:
               $sql = "SELECT
                            b.cod_bem
                        FROM                             patrimonio.bem as b
                        WHERE
                             b.num_placa = '".$numPlaca."'";
                $conn = new dataBaseLegado;
                $conn->abreBD();
                $conn->abreSelecao($sql);
                $conn->vaiPrimeiro();

                $codBem  = $conn->pegaCampo("cod_bem");
                $conn->limpaSelecao();
                $conn->fechaBD();

                if ($codBem > 0) {
                    $js .= 'd.frm.codBem.value = '.$codBem.';';
                } else {
                    $js .= "erro = true;\n";
                    $js .= 'mensagem += "Número do placa inválido!('.$numPlaca.').";';
                }

                executaFrameOculto($js);
                exit();

            break;

            case 2:
                               $sql = "SELECT
                             b.num_placa
                            ,b.cod_bem
                            ,b.descricao
                        FROM                             patrimonio.bem as b
                        WHERE
                             b.cod_bem  = '".$codBem."'";
                $conn = new dataBaseLegado;
                $conn->abreBD();
                $conn->abreSelecao($sql);
                $conn->vaiPrimeiro();

                $codBemPlaca  = $conn->pegaCampo("cod_bem");
                $numPlaca = $conn->pegaCampo("num_placa");
                $descricao = $conn->pegaCampo('descricao');
                $descricao = str_replace('"', '\"', str_replace(chr(13).chr(10)," ",$descricao));
                $descricao = str_replace('\'', '\\\'', str_replace(chr(13).chr(10)," ",$descricao));
                $conn->limpaSelecao();
                $conn->fechaBD();
                if ($descricao != '') {
                    $js .= "d.getElementById('lblDescricaoBem').innerHTML='".$descricao."';\n";
                }
                if ($numPlaca != '') {
                    $js .= 'd.frm.numPlaca.value = "'.$numPlaca.'";';
                    $js .= 'd.frm.dataBaixa.focus()';
                } else {
                    if (!$codBemPlaca >0) {
                        $js .= "erro = true;\n";
                        $js .= 'mensagem += "Código do bem inválido!( bem: '.$codBem.').";';
                        $js .= 'd.frm.ctrl_frm.value = "";';
                        $js .= 'd.frm.codBem.value = "";';
                    }
                }

                executaFrameOculto($js);
                exit();

            break;
        }

        if ($tipoBaixa=="unica") {
            $unica = "selected";
            $lote = "";

        } elseif ($tipoBaixa=="lote") {
            $unica = "";
            $lote = "selected";
        }

        if(!isset($dataBaixa))
            $dataBaixa = hoje();
?>
        <script type="text/javascript">
            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;

<?php
                if ($tipoBaixa=="lote") {
?>
                    campo1 = parseInt(document.frm.codInicial.value, 10);
                    campo2 = parseInt(document.frm.codFinal.value, 10);
                    if (campo1 > campo2) {
                        mensagem += "@O campo código inicial não pode ser maior que o campo código final!.";
                        erro = true;
                    }
<?php
                } else {
?>
                    campo = document.frm.codBem.value.length;
                    if (campo==0) {
                        mensagem += "@O campo código do bem é obrigatório.";
                        erro = true;
                    }

                    campo = document.frm.codBem.value;
                    if (isNaN(campo)) {
                        mensagem += "@O campo código do bem só aceita números.";
                        erro = true;
                    }
<?php
                }
?>
                campo = document.frm.dataBaixa.value.length;
                    if (campo==0) {
                        mensagem += "@O campo data da baixa é obrigatório.";
                        erro = true;
                    }

                campo = document.frm.motivoBaixa.value.length;
                    if (campo==0) {
                        mensagem += "@O campo motivo da baixa é obrigatório.";
                        erro = true;
                    }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);

            }// Fim da function Valida

            function busca_bem(cod)
            {
//                document.frm.controle.value = "<?=$ctrl;?>";
                document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=<?=$ctrl;?>";

                var f = document.frm;
                f.target = 'oculto';
                f.ctrl_frm.value = cod;
                f.submit();
            }

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
<?php
                    if ($tipoBaixa=="lote") {
?>
                        document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=<?=$ctrl+2;?>";
<?php
                    } else {
?>
                        document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=<?=$ctrl+1;?>";
<?php
                    }
?>
                    document.frm.submit();
                }
            }

        function LimpaDescricaoBem()
        {
          document.getElementById("lblDescricaoBem").innerHTML = "&nbsp;";
          document.frm.codBem.focus();
        }

        function fncEnviaBaixa()
        {
            document.frm.ctrl_frm.value="";
            document.frm.target        ="telaPrincipal";
            document.frm.submit();
        }
        </script>

        <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId()?>' onReset="LimpaDescricaoBem();">

            <input type="hidden" name="ctrl_frm" value=''>

        <table width="100%">

        <tr><td class=alt_dados colspan=2>Baixa de Bem</td></tr>

        <tr>
            <td class="label" width="20%" title ="Selecione o tipo da baixa do bem.">*Tipo de Baixa</td>
            <td class="field">
                <select name='tipoBaixa' style='width: 300px;' onChange="fncEnviaBaixa();">
                    <option value='unica' <?=$unica;?>>Baixa de um único bem</option>
                    <option value='lote'   <?=$lote;?>>Baixa em lote</option>
                </select>
            </td>
        </tr>
<?php
        if ($tipoBaixa=="lote") {
?>
    <script>
        function LimpaDescricaoBem()
        {
          document.frm.codInicial.focus();
        }
    </script>
            <tr>
                <td class="alt_dados" colspan=2 title="Informe o intervalo de códigos de bem.">Selecione o Intervalo</td>
            </tr>
            <tr>
                <td class=label title="Informe o código inicial do bem.">*Código Inicial</td>
                <td class=field>
                    <input type="text" name="codInicial" value="" size="10">
                </td>
            </tr>
            <tr>
                <td class=label title="Informe o código final do bem.">*Código Final</td>
                <td class=field>
                    <input type="text" name="codFinal" value="" size="10">
                </td>
            </tr>
<?php
        } else {
?>

                  <tr>
                     <td class="label" width="20%" title="Informe o código do bem.">*Código do Bem</td>
                     <td class="field">
                       <table width="100%" border="0" cellpadding=0 cellspacing=0>
                         <tr>
                           <td>
                    <input type="text" name="codBem" value="" size='10' maxlength="8" onKeyPress="return(isValido(this, event, '0123456789'));"onKeyUp="return autoTab(this, 8, event);" onChange="busca_bem(2)">&nbsp;
                           </td>
                           <td align="left" width="80%" id="lblDescricaoBem" name="sFornecedor" class="fakefield" valign="middle">&nbsp;</td>
                           <td width="8%">
                             <a href="javascript:procuraBemGP('frm','codBem','<?=Sessao::getId()?>');">
                               <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar bem" border="0" align="absmiddle">
                             </a>
                           </td>
                         </tr>
                      </table>
                     </td>
                  </tr>

             <tr>
                <td class="label" title="Informe a placa de identificação do bem.">Placa de Identificação</td>
                <td class="field">
                    <input type="text" name="numPlaca" value="" size='10' maxlength="8"  onKeyUp="return autoTab(this, 8, event);" onChange="busca_bem(1)">
                    <a href="javascript:procuraBemGP('frm','codBem','<?=Sessao::getId()?>');">
                    <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Procurar placa de identificação." border="0" align="absmiddle"></a>
                </td>
            </tr>

<?php
        }

 geraCampoData2("*Data da Baixa", "dataBaixa", $dataInicio, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data da baixa","Buscar data da baixa" );?>

        <tr>
            <td class="label" title="Informe o motivo da baixa." >*Motivo da Baixa</td>
            <td class="field">
                <textarea name='motivoBaixa' cols='35' rows='4'></textarea>
            </td>
        </tr>

        <tr>
            <td colspan='2' class='field'>
               <?=geraBotaoOk(); ?>
            </td>
        </tr>

        </table>

        </form>

<?php
    }//Fim da function formBaixaBem

}//Fim da classe
