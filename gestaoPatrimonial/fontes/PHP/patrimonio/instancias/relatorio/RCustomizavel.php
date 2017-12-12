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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php");

setAjuda("UC-03.01.19");

$ctrl = $_REQUEST['ctrl'];
$pagina = $_REQUEST['pagina'];
$ctrl_frm = $_REQUEST['ctrl_frm'];
$nom_atributo = $_REQUEST['nom_atributo'];

if (!(isset($ctrl))) {
    $ctrl = 0;
}

if (isset($pagina)) {
    $ctrl = 1;
}

switch ($ctrl_frm) {

    // preenche Natureza, Grupo e Especie
    case 2:
        include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosNGE.inc.php';
    break;
}

switch ($ctrl) {
    case 0:
?>

        <form name="frm" action="RCustomizavel.php?<?=Sessao::getId();?>" method="POST">
            <input type="hidden" name="ctrl" value=''>
            <input type="hidden" name="ctrl_frm" value=''>
            <!--os hiddens abaixo sao usados para a manipulacao dos filtros CALO e NGE.-->
            <input type="hidden" name="variavel" value=''>
            <input type="hidden" name="valor2" value=''>
            <input type="hidden" name="stCaminho" value=''>

        <table width="100%">
    <tr>
            <td class="alt_dados" colspan="2">Seleção de Atributos Dinâmicos</td>
        </tr>
         <?php
                    $aux=0;
                    // busca padrao teste
                    $sSQL = "
                        SELECT atributo_dinamico.nom_atributo
                             , atributo_dinamico.cod_atributo
                          FROM administracao.atributo_dinamico
                         WHERE atributo_dinamico.cod_cadastro = 1
                           AND atributo_dinamico.cod_modulo = 6
                      ORDER BY cod_atributo";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                    // monta combo com Naturezas
                    $comboCodNatureza = "";
                    while (!$dbEmp->eof()) {
                            $codAtributof  = trim($dbEmp->pegaCampo("cod_atributo"));
                            $nomAtributof  = trim($dbEmp->pegaCampo("nom_atributo"));
                            $dbEmp->vaiProximo();
                ?>
                <tr>
        <?$nomAtributofinal=mb_strtoupper($nomAtributof, 'UTF-8');  ?>
                <input type="hidden" name="nom_atributo[<?echo $codAtributof?>]" value="<?echo $nomAtributofinal?>">

                <td class="label"><?php echo $nomAtributof?></td>
                <td class="field">
                <?php
                $obChkAtributoDinamico = new CheckBox;
                $obChkAtributoDinamico->setName               ( "boAtributoDinamico".$aux."");
                $obChkAtributoDinamico->setValue              ( $codAtributof);
                $obChkAtributoDinamico->setChecked            (  false );
                $obChkAtributoDinamico->setTabIndex(0);
                $obChkAtributoDinamico->obEvento->setOnChange ( "preencheColunasDinamicas('".$nomAtributof."', this.value)");
                $obChkAtributoDinamico->obEvento->setOnClick  ( "verificaCheck(this.checked);");
                $obChkAtributoDinamico->show();
                $aux=$aux+1;

                ?>
                </td>
                <input type="hidden" name="cont" value='<?echo $aux?>'>
                </tr>
                <?php
                }
                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();

           ?>
               <input type="hidden" name="inModulo" value="6">
 <script>
  var conta = 0;

  function verificaCheck(obj)
  {
   if (obj) {
    if (conta >= 0 & conta <= 2) {
     conta += 1;
     if (conta == 3) {
      checkado(true);
     }
    }
   } else {
    conta -= 1;
    if (conta < 3) {
     checkado(false);
    }
   }
  }

  function checkado(bool)
  {
   var objCount = 1;
   for (x = 0;x < document.frm.elements.length;x++) {
    nome = document.frm.elements[x].name.substring(0,18);
    if (document.frm.elements[x].type == "checkbox" | nome != 'boAtributoDinamico') {
     obj = eval("document.all.boAtributoDinamico" + objCount);
     if (obj) {
      if (bool) {
       if (obj.checked == false) {
        obj.disabled = true;
       }
      } else {
        obj.disabled = false;
      }
     }
     objCount += 1;
    }
   }
  }

    function Limpar()
        {
            var grupo = $('inCodGrupo');

            var tam = grupo.options.length;
            while (tam >= 1) {
                    grupo.options[tam] = null;
                    tam = tam - 1 ;
            }

            var especie = $('inCodEspecie');

            var tam = especie.options.length;
            while (tam >= 1) {
                    especie.options[tam] = null;
                    tam = tam - 1 ;
            }

        limpaSelect($('filtro'),0);
        $('filtro').options[0] = new Option('Selecione','xxx');

        limpaSelect($('ordenacao'),0);
        $('ordenacao').options[0] = new Option('Selecione','xxx');

            document.frm.reset();

    }
 </script>

   <tr>
        <td class="alt_dados" colspan="2">Seleção de Colunas ( Marque ao Menos uma Opção! )</td>
    </tr>
    <td class="label">Data de Baixa</td>
            <td class="field">
          <?php
            $obChkDataBaixa = new CheckBox;
            $obChkDataBaixa->setName           ( "boDataBaixa"  );
            $obChkDataBaixa->setChecked        ( ($boDataBaixa == true) );
            $obChkDataBaixa->setTabIndex       (0);
            $obChkDataBaixa->obEvento->setOnClick( "preencheColunas(this.name);");
            $obChkDataBaixa->setValue(1);
            $obChkDataBaixa->show();
           ?>
        </td>
         <tr>
        <td class="label">Empenho</td>
            <td class="field">
          <?php
               $obChkEmpenho = new CheckBox;
               $obChkEmpenho->setName           ( "boEmpenho"  );
               $obChkEmpenho->setChecked        ( ($boEmpenho == true) );
               $obChkEmpenho->setTabIndex       (0);
               $obChkEmpenho->obEvento->setOnChange( "preencheColunas(this.name);");
               $obChkEmpenho->show();
          ?>
        </td>
     </tr>
         <tr>
        <td class="label">Valor</td>
            <td class="field">
          <?php
               $obChkValor = new CheckBox;
               $obChkValor->setName           ( "boValor"  );
               $obChkValor->setChecked        ( ($boValor == true) );
               $obChkValor->setTabIndex       (0);
           $obChkValor->obEvento->setOnChange( "preencheColunas(this.name);");
           $obChkValor->show();
          ?>
        </td>
    </tr>
    <tr>
        <td class="label">Aquisição</td>
            <td class="field">
          <?php
               $obChkAquisicao = new CheckBox;
               $obChkAquisicao->setName           ( "boAquisicao"  );
               $obChkAquisicao->setChecked        ( ($boAquisicao == true) );
               $obChkAquisicao->setTabIndex       (0);
           $obChkAquisicao->obEvento->setOnChange( "preencheColunas(this.name);");
           $obChkAquisicao->show();
          ?>
        </td>
    </tr>
    <tr>
        <td class="label">Número da Placa</td>
            <td class="field">
          <?php
               $obChkPlaca = new CheckBox;
               $obChkPlaca->setName           ( "boPlaca"  );
               $obChkPlaca->setChecked        ( ($boPlaca == true) );
               $obChkPlaca->setTabIndex       (0);
           $obChkPlaca->obEvento->setOnChange( "preencheColunas(this.name);");
           $obChkPlaca->show();
          ?>
        </td>
    </tr>
    <tr>
        <td class="label">Nota Fiscal</td>
            <td class="field">
          <?php
               $obChkPlaca = new CheckBox;
               $obChkPlaca->setName           ( "boNotaFiscal"  );
               $obChkPlaca->setChecked        ( ($boPlaca == true) );
               $obChkPlaca->setTabIndex       (0);
               $obChkPlaca->obEvento->setOnChange( "preencheColunas(this.name);");
               $obChkPlaca->show();
          ?>
        </td>
    </tr>
    <tr>
            <td class="alt_dados" colspan="2">Dados para o Filtro</td>
        </tr>
        <tr>
            <td class="label" vidth="20%" title="Informe a Entidade do Bem.">Entidade</td>
            <td class="field" width="80%">
                <select name='codEntidade' style="width:320px">
                    <option value='xxx' SELECTED>Selecione</option>
<?php
                    // busca Naturezas cadastradas

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
                    $comboCodNatureza = "";
                    while (!$dbEmp->eof()) {
                            $codEntidadef  = trim($dbEmp->pegaCampo("numcgm"));
                            $nomEntidadef  = trim($dbEmp->pegaCampo("nom_cgm"));
                            $chave = $codEntidadef;
                            $dbEmp->vaiProximo();
                            $comboCodEntidade .= "<option value='".$chave."'";
                            if (isset($codEntidade)) {
                                if ($chave == $codEntidade) {
                                        $comboCodEntidade .= " SELECTED";
                                        $nomEntidade = $nomEntidadef;
                                        }
                                }
                            $comboCodEntidade .= ">".$nomEntidadef."</option>\n";
                            }
                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboCodEntidade;
?>
                </select>
                <input type="hidden" name="nomEntidade" value="">
            </td>
        </tr>
        </table>
<?PHP
        include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
        $obFormulario = new Formulario;
        $obFormulario->setForm(null);
        $obIMontaOrganograma = new IMontaOrganograma(true);
        $obIMontaOrganograma->setCadastroOrganograma(true);
        $obIMontaOrganograma->geraFormulario($obFormulario);
        $obFormulario->montaHTML();
        echo $obFormulario->getHTML();
?>
         <table width='100%'>
<?PHP
        include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php" );
        include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php" );
        include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php" );

        $obTPatrimonioNatureza = new TPatrimonioNatureza();
        $obTPatrimonioNatureza->recuperaMaxNatureza( $rsMaxCodNatureza );
        $tamanhoMaxNat = strlen($rsMaxCodNatureza->arElementos[0]['max']) + 1;

        $obTPatrimonioGrupo = new TPatrimonioGrupo();
        $obTPatrimonioGrupo->recuperaMaxGrupoCombo( $inMaxCodGrupo );
        $tamanhoMaxGr = strlen($inMaxCodGrupo->arElementos[0]['max'])  + 1;

        $obTPatrimonioEspecie = new TPatrimonioEspecie();
        $obTPatrimonioEspecie->recuperaMaxEspecie( $rsMaxCodEspecie );
        $tamanhoMaxEs = strlen($rsMaxCodEspecie->arElementos[0]['max']) + 1;

        $valorMascara = "'".str_pad('9',$tamanhoMaxNat-1,'9').'.'.str_pad('9',$tamanhoMaxGr-1,'9').'.'.str_pad('9',$tamanhoMaxEs-1,'9')."'";
        $tamanhoCampo = $tamanhoMaxNat + $tamanhoMaxGr + $tamanhoMaxEs;

?>

                <input type="hidden" name="nomOrgao" value="">
            </td>
        </tr>

        <tr>
                <td class='label'>
                        Classificação
                </td>
                <td class='field'>
                        <input id="stCodClassificacao" type="text" align="left" size="<?PHP echo $tamanhoCampo; ?>" maxlength="<?PHP echo $tamanhoCampo; ?>" onblur="JavaScript:preencheTodosNGE(this.value );" onkeyup="JavaScript:mascaraDinamico(<?PHP echo $valorMascara; ?>, this, event);" tabindex="1" name="stCodClassificacao"/>
                </td>
        </tr>

        <tr>
            <td class="label" width="20%" title="Informe a Natureza do Bem.">Natureza</td>
            <td class='field' width="80%">
                <select name='codNatureza' onChange="javascript: preencheNGE('codNatureza', this.value);" style="width:320px">
                    <option value="xxx" SELECTED>Selecione</option>
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
                            $comboCodNatureza .= ">".$codNaturezaf." - ".$nomNaturezaf."</option>\n";
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
            <td class="label" width="20%" title="Informe o Grupo para o filtro.">Grupo</td>
            <td class="field">
                <select id='inCodGrupo' name="codGrupo" onChange="javascript: preencheNGE('codGrupo', this.value);" style="width:320px">
                        <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomGrupo" value="">
            </td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Informe a espécie para o filtro.">Espécie</td>
            <td class="field">
                <select id='inCodEspecie' name="codEspecie" onChange="javascript: preencheNGE('codEspecie', this.value);" style="width:320px" >
                        <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomEspecie" value="">
            </td>
        </tr>
         <tr>
            <td class="label" title='Informe a data inicial de compra para o filtro.' >*Data Inicial</td>
            <td class="field">
         <?php

              $obDtInicial = new Data;
              $obDtInicial->setRotulo                ( "Data Inicial" );
              $obDtInicial->setName                  ( "dtInicial" );
              $obDtInicial->setValue                 ( $dtInicial  );
              $obDtInicial->setNull                  ( false );
              $obDtInicial->setTabIndex              (0);
              $obDtInicial->obEvento->setOnBlur("if ( !verificaData( this ) ) {alertaAviso('@Data inválida('+this.value+') !','form','erro','".Sessao::getId()."'); this.value = '';}");

              $obDtInicial->show();
         ?>
            <a href="javascript: MostraCalendario('frm','dtInicial','Sessao::getId()');">
       <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/calendario.gif"  border="0" title= "Buscar data inicial" align="absmiddle"></a>
            </td>

         </tr>
        <tr>
            <td class="label" title='Informe a data final de compra para o filtro.'>*Data Final</td>
            <td class="field">
                <?php
                $obDtFinal = new Data;
                $obDtFinal->setRotulo                ( "Data Final" );
                $obDtFinal->setName                  ( "dtFinal" );
                $obDtFinal->setValue                 ( $dtFinal  );
                $obDtFinal->setTabIndex              (0);
                $obDtFinal->obEvento->setOnBlur("if ( !verificaData( this ) ) {alertaAviso('@Data inválida('+this.value+') !','form','erro','".Sessao::getId()."'); this.value = '';}");
                $obDtFinal->setNull                  ( false );
                $obDtFinal->show();
                ?>
            <a href="javascript: MostraCalendario('frm','dtFinal','Sessao::getId()');">
       <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/calendario.gif"  border="0" title= "Buscar data final" align="absmiddle"></a>
            </td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Selecione a ordenação para o filtro.">*Ordenação</td>
            <td class="field">
                <select name="ordenacao" id="ordenacao" style="width:320px"  >
                        <option value="0" SELECTED>Selecione</option>
                </select>
            </td>
        </tr>
         <tr>
            <td class="label">Ordenar por Data</td>
                <td class="field">
                <?php
                $obChkOrdenaData = new CheckBox;
                $obChkOrdenaData->setName               ( "boOrdenaData");
                $obChkOrdenaData->setChecked            (  false );
                $obChkOrdenaData->setTabIndex           (0);
                $obChkOrdenaData->show();
                ?>
                </td>
          </tr>
           <tr>
            <td class="label">Ordenar por Código</td>
                <td class="field">
                <?php
                $obChkOrdenaCodigo = new CheckBox;
                $obChkOrdenaCodigo->setName               ( "boOrdenaCodigo");
                $obChkOrdenaCodigo->setChecked            (  false );
                $obChkOrdenaCodigo->setTabIndex           (0);
                $obChkOrdenaCodigo->obEvento->setOnBlur( "focusFiltro('filtro');"  );
                $obChkOrdenaCodigo->show();
                ?>
                </td>
          </tr>
          <tr>
            <td class="label" width="20%" title="Selecione o filtro do atributo.">Coluna para o Filtro</td>
            <td class="field">
                <?php
                    $obSelect = new Select();
                    $obSelect->setName( 'filtro' );
                    $obSelect->setId( 'filtro' );
                    $obSelect->setStyle( 'width:320px;' );
                    $obSelect->addOption( 'xxx', 'Selecione' );
                    $obSelect->obEvento->setOnChange( 'limpaFiltro();'  );
                    $obSelect->obEvento->setOnBlur( "focusFiltro1();"  );
                    $obSelect->show();
                ?>
<!--                <select name="filtro" id="filtro" style="width:320px" onchange="" >
                        <option value="xxx" SELECTED>Selecione</option>
                </select>-->
            </td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Informe o intervalo da pesquisa do filtro.">Filtro</td>
            <td class="field"><INPUT type="text" name="valor_filtro1" id="valor_filtro1" value="" size="15" maxlength="15" > a <INPUT type="text" name="valor_filtro2" id="valor_filtro2" value="" size="15" maxlength="15" ></td>
        </tr>

        <tr>
            <td class="label" title='Informe a descrição do título do relatório.'>*Título</td>
            <td class="field">
                <?php
                $obTxtTitulo = new TextBox;
                $obTxtTitulo->setName               ( "stTitulo" );
                $obTxtTitulo->setValue              ( $stTitulo  );
                $obTxtTitulo->setMaxLength          ( 50         );
                $obTxtTitulo->setSize               ( 50         );
                $obTxtTitulo->setTabIndex           ( 0          );
                $obTxtTitulo->show();
                ?>
            </td>
          </tr>
        <tr>
        <td class="label" title="Selecione se voce deseja que campos com grande quantidade de texto seja expandido em mais linhas" >Expandir Campos no Relatório</td>
                <td class="field">
                <?php
                $obChkExpCampos = new CheckBox;
                $obChkExpCampos->setName               ( "boExpandeCampos");
                $obChkExpCampos->setChecked            (  false );
                $obChkExpCampos->show();
                ?>
                </td>
          </tr>
          
          <tr>
            <td class="label" title='Demonstrar Bens Baixados:'>*Demonstrar Bens Baixados:</td>
            <td class="field">
                <?php
                $obRadioBemBaixadoSim = new Radio;
                $obRadioBemBaixadoSim->setName        ( "stRBemBaixado" );
                $obRadioBemBaixadoSim->setId          ( "stRBemBaixado" );
                $obRadioBemBaixadoSim->setValue       ( "sim" );
                $obRadioBemBaixadoSim->setLabel       ( "Sim" );
                $obRadioBemBaixadoSim->setChecked     (  false );                
                $obRadioBemBaixadoSim->show();

                $obRadioBemBaixadoNao = new Radio;
                $obRadioBemBaixadoNao->setName        ( "stRBemBaixado" );
                $obRadioBemBaixadoNao->setId          ( "stRBemBaixado" );
                $obRadioBemBaixadoNao->setValue       ( "nao" );
                $obRadioBemBaixadoNao->setLabel       ( "Não" );
                $obRadioBemBaixadoNao->setChecked     (  false );                
                $obRadioBemBaixadoNao->show();

                $obRadioBemBaixadoTodos = new Radio;
                $obRadioBemBaixadoTodos->setName        ( "stRBemBaixado" );
                $obRadioBemBaixadoTodos->setId          ( "stRBemBaixado" );
                $obRadioBemBaixadoTodos->setValue       ( "todos" );
                $obRadioBemBaixadoTodos->setLabel       ( "Todos" );
                $obRadioBemBaixadoTodos->setChecked     (  true );                
                $obRadioBemBaixadoTodos->show();               
                ?>
            </td>
          </tr>

       <tr>
       <td class="field" colspan="2"><?php geraBotaoOk2(1,1); ?>
       </td>
       </tr>
      </table>
      </form>
      <script type='text/javascript'>
              function Valida()
              {
            var mensagem = "";
            var erro = false;
            var VdtInicial;
            var VdtFinal;
            var VOrdenacao;
            var VTitulo;
            var Vcont;
            var NomeAtributo;
            var nome;
            var Contador = 0;

        VdtInicial = document.frm.dtInicial.value.split("/");
        VdtInicial = VdtInicial[2]+VdtInicial[1]+VdtInicial[0];
        VdtFinal   = document.frm.dtFinal.value.split("/");
        VdtFinal   = VdtFinal[2]+VdtFinal[1]+VdtFinal[0];
        VOrdenacao = document.frm.ordenacao.value;
        VTitulo    = document.frm.stTitulo.value;
        Vcont      = document.frm.cont.value;
        VFiltro    = document.frm.filtro.value;
        VFiltro1   = document.frm.valor_filtro1.value;
        VFiltro2   = document.frm.valor_filtro2.value;
        VOrganograma = document.frm.inCodOrganogramaOrganograma.value;

        for (i=0;i<document.frm.elements.length ;i++) {
             nome = document.frm.elements[i].name;

            if (nome.substring(0,18) == 'boAtributoDinamico') {
               if (document.frm.elements[i].checked == true) {
                  Contador = Contador + 1;
               }
            }
        }

        if (Contador > '3') {
            mensagem +="Pode-se selecionar no máximo três atributos dinâmicos.";
            erro = true;
         }

        // Avisa o usuário para selecionar ao menos uma coluna
        if (
            (document.frm.boDataBaixa.checked == false)  &&
            (document.frm.boEmpenho.checked == false)    &&
            (document.frm.boValor.checked == false)      &&
            (document.frm.boAquisicao.checked == false)  &&
            (document.frm.boPlaca.checked == false)      &&
            (document.frm.boNotaFiscal.checked == false)
           ){
          mensagem +="Selecione ao menos uma coluna para exibir no relatório!";
          erro = true;
        } else if (VOrganograma == '') {
              mensagem +="Selecione o *Organograma para poder exibir o relatório.";
              erro = true;
        } else if (document.frm.inCodOrganogramaClassificacao.value == '0.00.00') {
            mensagem += "Digite a *Classificação do *Organograma para poder exibir o relatório.";
             erro = true;
        } else if (document.frm.inCodOrganogramaClassificacao.value == '00.00') {
              mensagem += "Digite a *Classificação do *Organograma para poder exibir o relatório.";
               erro = true;
          } else if (VdtInicial == '' | isNaN(VdtInicial)) {
               mensagem +="O campo *Data Inicial deve ser preenchido!";
                erro = true;
        } else if (VdtFinal == '' | isNaN(VdtFinal)) {
          mensagem +="O campo *Data Final deve ser preenchido!";
          erro = true;
        } else if (VOrdenacao == 'xxx' || VOrdenacao == '0') {
          mensagem +="O campo *Ordenação deve ser preenchido!";
          erro = true;
        } else if (VTitulo == '') {
          mensagem +="O campo *Título deve ser preenchido!";
          erro = true;
        /*} else if (VFiltro != 'xxx' || VFiltro != '0') {
              mensagem +="O campo coluna do filtro deve ser preenchido!";
              erro = true;*/
        } else if (VFiltro != 'xxx') {
            if (VFiltro1 =='') {
                mensagem +="O campo valor inicial do filtro deve ser preenchido!";
                erro = true;
            } else if (VFiltro2 =='') {
              mensagem +="O campo valor final do filtro deve ser preenchido!";
              erro = true;
            }
        } else if (VdtFinal < VdtInicial) {
          mensagem +="A data inicial deve menor que a data final.";
          erro = true;
        } else if (
                (document.frm.boDataBaixa.checked == true)  &&
                (document.frm.boEmpenho.checked == true)    &&
                (document.frm.boValor.checked == true)      &&
                (document.frm.boAquisicao.checked == true)  &&
                (document.frm.boPlaca.checked == true)      &&
                (document.frm.boNotaFiscal.checked == true)
               )
        {
             mensagem += "Pode-se selecionar no máximo quatro colunas para a geração do relatório.";
             erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
           return !(erro);
        }

        function Salvar()
        {
            if (Valida()) {
                    document.frm.action = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/popups/relatorio/OCRelatorio.php?<?=Sessao::getId();?>";
                    document.frm.target = 'oculto';
                    document.frm.stCaminho.value = "../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/OCRelatorioCustomizavel.php";
                    document.frm.submit();
            }
        }

// preenche os combos de Natureza, Grupo e Especie
        function preencheNGE(variavel, valor)
        {
            document.frm.action ="";
            document.frm.target = "oculto";
            document.frm.ctrl.value = '2';
            document.frm.ctrl_frm.value = "2";
            document.frm.variavel.value = variavel;
            document.frm.valor2.value = valor;
            document.frm.submit();
        }

        function preencheTodosNGE(valor)
        {
            document.frm.action ="";
            document.frm.target = "oculto";
            document.frm.ctrl.value = '2';
            document.frm.ctrl_frm.value = "2";
            document.frm.variavel.value = 'NatGrpEsp';
            document.frm.valor2.value = valor;
            document.frm.submit();
        }

        function preencheColunasDinamicas(nome,valor)
        {
            var i = 0;
            var deletarPosicaoOrdenacao = '';
            var deletarPosicaoFiltro = '';

            for (i=0; i< $('ordenacao').length; i++) {
            if ($('ordenacao').options[i].value == valor) {
                deletarPosicaoOrdenacao = i;
            }
            }

            var j = 0;
            for (j=0; j< $('filtro').length; j++) {
                if ($('filtro').options[j].value == valor) {
                deletarPosicaoFiltro = j;
                }
            }

            if ( (deletarPosicaoOrdenacao == '') && (deletarPosicaoFiltro == '') ) {
            $('filtro').options[$('filtro').length] = new Option( nome,valor );
            $('ordenacao').options[$('ordenacao').length] = new Option( nome,valor );
            } else {
            $('ordenacao').options[deletarPosicaoOrdenacao].remove(deletarPosicaoOrdenacao);
            $('filtro').options[deletarPosicaoFiltro].remove(deletarPosicaoFiltro);
            }
        }

        function preencheColunas(stElemento)
        {
            var boErro = false;

            //bloquiar a opção 'Não' do campo '*Demonstrar Bens Baixados' quando a Data da Baixa for exibida no relatorio
            if ( jQuery("input:checkbox[name='boDataBaixa']").is(":checked") ) {
                jQuery("input:radio[name='stRBemBaixado']").each(function(){
                    if ( jQuery(this).val() == "nao" ){
                        jQuery(this).prop('disabled',true);
                    }                                        
                });    
            }else{                    
                jQuery("input:radio[name='stRBemBaixado']").each(function(){
                    if ( jQuery(this).val() == "nao" ){
                        jQuery(this).prop('disabled',false);
                    }                                        
                });    
            }

            for ( i=0; i< $('filtro').length;i++) {
                if ( $('filtro').options[i].value == stElemento ) {
                    $('filtro').options[i].remove(i);
                    boErro = true;
                }
            }

            for ( i=0; i< $('ordenacao').length;i++) {
                if ( $('ordenacao').options[i].value == stElemento ) {
                    $('ordenacao').options[i].remove(i);
                    boErro = true;
                }
            }

            if (boErro == true) {
                return false;
            }
            
            switch (stElemento) {
            case 'boValor' :
                $('filtro').options[$('filtro').length] = new Option( 'Valor','boValor' );
                $('ordenacao').options[$('ordenacao').length] = new Option( 'Valor','boValor' );
            break;

            case 'boPlaca' :
                $('filtro').options[$('filtro').length] = new Option( 'Placa','boPlaca' );
                $('ordenacao').options[$('ordenacao').length] = new Option( 'Placa','boPlaca' );
            break;

            case 'boAquisicao' :
                $('filtro').options[$('filtro').length] = new Option( 'Aquisição','boAquisicao' );
                $('ordenacao').options[$('ordenacao').length] = new Option( 'Aquisição','boAquisicao' );
            break;

            case 'boEmpenho' :
                $('filtro').options[$('filtro').length] = new Option( 'Empenho','boEmpenho' );
                $('ordenacao').options[$('ordenacao').length] = new Option( 'Empenho','boEmpenho' );
            break;

            case 'boDataBaixa' :
                $('filtro').options[$('filtro').length] = new Option( 'Data de Baixa','boDataBaixa' );
                $('ordenacao').options[$('ordenacao').length] = new Option( 'Data de Baixa','boDataBaixa' );
            break;

            case 'boNotaFiscal' :
                $('filtro').options[$('filtro').length] = new Option( 'Nota Fiscal','boNotaFiscal' );
                $('ordenacao').options[$('ordenacao').length] = new Option( 'Nota Fiscal','boNotaFiscal' );
            break;

            }

            // Caso só tenha uma coluna seleciona, seta como ordenação.
            if ($('ordenacao').length == 2) {
            $('ordenacao').options[1].selected = true;
            } else {
            $('ordenacao').options[0].selected = true;
            }

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

        function limpaFiltro()
        {
            $('valor_filtro1').value = '';
            $('valor_filtro2').value = '';
        }

        function focusFiltro(nome)
        {
            $(nome).focus();
        }

        function focusFiltro1()
        {
            $('valor_filtro1').focus();
        }
    </script>
<?php
    break;
    case 1:

    break;
}

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
