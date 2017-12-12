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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

class interfaceHtmlLegada
{
    //Variáveis da classe
    public $html, $js, $sJava, $sJavaValida, $exercicio,
        $chave, $aux, $abas, $erro, $arquivo, $phpSessaoId;

    //Variável tipo vetor que recebe os campos e suas características
    public $campos;

    //Variável tipo vetor que recebe os campos do tipo hidden
    public $camposOcultos;

    //Variável tipo vetor que recebe as características da tabela que compõe o formulário
    public $tabela;

    //Variáveis tipo vetor que recebe os atributos de um formulário
    public $form;

    //Flags que controlam características do formulário
    public $flagCamposObrigatorios; //Determina se no fim do formulário aparece a legenda "*Campos Obrigatórios"
    public $flagValidacao; //Determina se o formulário será validado
    public $flagSalvar; // Determina se o botão Ok chamará a função Salvar() ou será um simples submit
    public $flagGravaCampo; //Determina se o último campo criado será gravado como novo ou gravado em um campo criado anteriormente
    public $flagSubmit; // Determina se os botões Ok e Limpar aparecerão no final do formulário
    public $flagBotaoLimpar; // Determina se o botão Limpar aparecerá ao lado do botão Ok
    public $flagReset; //Determina se o botão limpar será do tipo reset ou se vai chamar a função limpar()

    //Flag que informa se o método geraFormulario deve imprimir o código gerado.
    //Caso resolva não imprimir, usar as variávies $this->html e $this->js
    public $flagPrint;

/**************************************************************************
 Método construtor. Inicializa as variáveis de classe, cada qual
 com seu respectivo valor padrão.
***************************************************************************/
function interfaceHtmlLegada()
{
    global $PHP_SELF; //Torna visível a variável "PHP_SELF" dentro do método construtor
     //Torna visível o objeto "sessao" dentro do método construtor

    $this->html = "";
    $this->js = "";
    $this->sJava = "";
    $this->sJavaValida = "";
    $this->exercicio = Sessao::getExercicio(); //Grava o exercício que o usuário usou no login
    $this->chave = "";
    $this->aux = "";
    $this->abas = "";
    $this->arquivo = $PHP_SELF; //Grava como valor padrão o arquivo que chamou a classe
    $this->phpSessaoId = Sessao::getId(); //Grava como valor padrão a variável que registra a sessão no browser

    $this->campos = array(); //Inicia a variável que receberá os campos

    $this->camposOcultos = array(); //Inicia a variável que receberá os campos ocultos

    $this->tabela = array();
    $this->tabela['atributos'] = array('width'=>'100%');
    $this->tabela['estiloLabel'] = "label";
    $this->tabela['estiloField'] = "field";
    $this->tabela['widthLabel'] = "30%";
    $this->tabela['widthField'] = "70%";

    $this->form = array();
    $this->form['name'] = "frm";
    $this->form['method'] = "post";
    $this->form['target'] = "telaPrincipal";
    $this->form['action'] = "";

    $this->flagCamposObrigatorios = true; //Determina se será exibida a legenda "*Campos Obrigatórios"
    $this->flagValidacao = true; //Determina se o formulário será validado.
    $this->flagPrint = true; //Determina se os códigos gerados serão impressos ou não.
    $this->flagGravaCampo = true; //Determina se um campo criado será gravado como novo ou anexado a um campo existente.
    $this->flagSubmit = true; //Determina se o formulário exibirá botões ok e limpar no fim da página.
    $this->flagSalvar = true; //Determina se o botao "ok" chamará a função js "Salvar" ou será um simples botão de submit.
    $this->flagBotaoLimpar = true; //Determina se o formulário terá o botão limpar.
    $this->flagReset = true; //Determina se o botão limpar será do tipo "reset" ou chamará a função js "limpa" que força todos os campos a ficarem em branco.

    $this->erro = ""; //Variável para gravar mensagens do tratamento de exceções
}//Fim do método construtor

/**************************************************************************
 Método que grava a configuração da tabela para cada campo
 Estas propriedades são iniciadas com valores padrão e podem ser alteradas
 entre um campo e outro chamando este método
**************************************************************************/
function setaEstiloTabela($estiloLabel,$estiloField,$widthLabel,$widthField)
{
    $this->tabela['estiloLabel'] = $estiloLabel;
    $this->tabela['estiloField'] = $estiloField;
    $this->tabela['widthLabel'] = $widthLabel;
    $this->tabela['widthField'] = $widthField;
}

/**************************************************************************
 Método que gera um formulário completo em HTML
***************************************************************************/
function geraFormulario()
{
    if ($this->form['action'] == "") {
        $this->form['action'] = $this->arquivo."?".$this->phpSessaoId;
    }

    $this->html = "\n ";

    $this->js = $this->geraJavaScript();

    //Adiciona abas caso tenham sido criadas com o método criaAbas()
    $this->html .= $this->abas;

    //Inicia o formulário
    $this->html .= '<form ';
    foreach ($this->form as $atributo=>$valor) {
        $this->html .= ' '.$atributo.'="'.$valor.'" ';
    }
    $this->html .= '>';

    //Chama o método que gera os campos ocultos do formulário -- O campo exercício sempre estará presente
    $this->geraCamposOcultos();
    $this->html .= '<input type="hidden" name="sessaoExercicio" value="'.$this->exercicio.'">';

    //Inicia a tabela html
    $this->html .= '<table';
    foreach ($this->tabela['atributos'] as $atributo=>$valor) {
        $this->html .= ' '.$atributo.'="'.$valor.'" ';
    }
    $this->html .= '>';

    if (array_key_exists('cabecalho',$this->tabela)) {
        $this->html .= '<tr><td class="alt_dados" colspan="2"> '.$this->tabela['cabecalho'].' </td></tr>';
    }

    //Chama o método que gera os campos que compõe o formulário
    $this->geraCampos();

    //Gera os botões ok e limpar
    if ($this->flagSubmit) {
        $this->html .= '<tr><td colspan="2" class="field">';
        $this->html .= $this->geraBotaoOk();
        $this->html .= '</td></tr>';
    }

    $this->html .= '</table>';
    $this->html .= '</form>';

    //Se for solicitado imprime o código HTML e o javascript
    if ($this->flagPrint) {
        print $this->js;
        print $this->html;
    }
}

/**************************************************************************
 Método que gera um formulário completo em HTML
***************************************************************************/
function geraCampos()
{
    $html = "\n ";
    foreach ($this->campos as $campo) {
      if (array_key_exists('generico',$campo)) {
        $html .= $campo['codHtml'];
      } else {
        if ($campo['obrigatorio']) {
            $nome = "*".$campo['label'].":";
        } else {
            $nome = $campo['label'].":";
        }

        $html .= '<tr><td class="'.$campo['estiloLabel'].'" width="'.$campo['widthLabel'].'" ';
        if (strlen($campo['dica']) > 0) {
            $html .= ' title="'.$campo['dica'].'" ';
        }
        $html .= $campo['complementoLabel'];
        $html .= '>'.$nome.'</td>';
        $html .= '<td class="'.$campo['estiloField'].'" width="'.$campo['widthField'].'" '.$campo['complementoField'].' >';

        //Cria todos os itens de formulário existentes para o campo
        foreach ($campo['input'] as $input) {
          switch ($input['tipoInput']) {
            case 'texto':
            $html .= '<input '.$input['complemento'].' ';
            foreach ($input['atributos'] as $atributo=>$valor) {
                $html .= ' '.$atributo.'="'.$valor.'" ';
            }
            $html .= ">";
            break;
            case 'combo':
                $html .= $input['codHtml'];
            break;
            case 'textarea':
                $html .= '<textarea '.$input['complemento'].' ';
                foreach ($input['atributos'] as $atributo=>$valor) {
                    $html .= ' '.$atributo.'="'.$valor.'" ';
                }
                $html .= ">";
                $html .= $input['value'];
                $html .= "</textarea>";
            break;
            case 'checkbox':
                $html .= '<input type="checkbox" '.$input['complemento'].' ';
                foreach ($input['atributos'] as $atributo=>$valor) {
                    $html .= ' '.$atributo.'="'.$valor.'" ';
                }
                $html .= ">&nbsp;".$input['nome'];
            break;
            case 'radio':
                $html .= '<input type="radio" '.$input['complemento'].' ';
                foreach ($input['atributos'] as $atributo=>$valor) {
                    $html .= ' '.$atributo.'="'.$valor.'" ';
                }
                $html .= ">&nbsp;".$input['nome'];
            break;
            default:
                $html .= $input['codHtml'];
            break;
          }//Fim switch
          //Inclui link de busca se houver
          if (array_key_exists('link',$input)) {
            $html .= $input['link'];
          }
        }//Fim foreach

        $html .= "</td></tr> \n";
      }
    }

    $this->html .= $html;
}

/**************************************************************************
 Método que cria um novo campo com seus respectivos atributos
***************************************************************************/
function criaCampo($tipoCampo,$name,$label="",$type="",$size="9",$maxlength="9",
                   $value="",$obrigatorio=0,$complemento="",$dica="") {
    //Cria os dados para informações do campo
    $campo = array();
    $campo['label'] = $label;
    $campo['obrigatorio'] = $obrigatorio;
    $campo['dica'] = $dica;

    //Cria o item de formulário do campo
    $input = array();
    $input['tipoInput'] = "texto";
    $input['obrigatorio'] = $obrigatorio;
    $input['complemento'] = $complemento;

    //Cria um vetor para os atributos
    $atr = array();
    $atr['type'] = "text";
    $atr['name'] = $name;
    $atr['size'] = $size;
    $atr['maxlength'] = $maxlength;
    $atr['value'] = $value;

        //Default: "texto". Pode ser "numero", "chave", "senha", "digito"
        switch ($tipoCampo) {
        case 'texto':
            $atr['type'] = "text";
        break;
        case 'senha': //***
            $atr['type'] = "password";
        break;
        case 'numero': //123
            $atr['onKeyPress'] = "return(isValido(this, event, '0123456789'));";
        break;
        case 'exercicio': // 0000
            $atr['size'] = "5";
            $atr['maxlength'] = "4";
            $atr['onKeyPress'] = "return(isValido(this, event, '0123456789'));";
        break;
        case 'chave': //3.5.1.8
            $atr['onKeyPress'] = "return(isValido(this, event, '0123456789.'));";
        break;
        case 'digito': //1-9
            $atr['onKeyPress'] = "return(isValido(this, event, '0123456789-'));";
        break;
        case 'data': // "1/1/0000"
            $atr['onKeyPress'] = "return(isValido(this, event, '0123456789/'));";
            $atr['onDblClick'] = "retornaData(this);";
        break;
        case 'decimal': //1000,00
            $atr['onKeyPress'] = "return(formataNumeroDecimais(this,'',',',event));";
        break;
        case 'decimal-mil': //1.000,00
            $atr['onKeyPress'] = "return(formataNumeroDecimais(this,'.',',',event));";
        break;
        }

    //Se foi especificado um atributo type diferente, força alteração do atributo
    if (strlen($type) > 0) {
        $atr['type'] = $type;
    }

    //Grava os atributos no item de formulário
    $input['atributos'] = $atr;

    $campo['input'][] = $input;

    $this->gravaCampo($campo);
}

/**************************************************************************
 Método para criar campo tipo data com ícone para buscar data
 Obs: É um atalho para o método criaCampo
**************************************************************************/
function criaCampoData($name,$label="",$value="",$obrigatorio=0,$complemento="",$readonly=True,$dica="")
{
    //Força a opção somente leitura para o campo data
    if ($readonly) {
        $complemento .= " readonly='' ";
    }

    //Usa a função criaCampo para gerar um novo input para a data
    $this->criaCampo("data",$name,$label,"","10","10",$value,$obrigatorio,$complemento,$dica);
    //Cria o link de busca para o calendário
    $this->criaLink("data",$name);
}

/**************************************************************************
 Método para criar um link que chama uma pop-up de busca
 Obs: O parâmetro "$campos" deve ser passado em uma única string separado
      por vírgulas
**************************************************************************/
function criaLink($tipoBusca,$campos="",$title="",$complemento="",$imagem="",
                  $imgWidth="20",$imgHeight="20") {
    //Separa os campos
    $campo = explode(",",$campos);
    $link = "&nbsp;";
    $img = "procuracgm.gif";
    $func = "";
    $alt = "";

    //Determina a função a ser chamada de acordo com a variável "$tipoBusca"
    switch ($tipoBusca) {
      case 'cgm-geral':
        $func = "javascript:procurarCgm('".$this->form['name']."','".$campo[0]."','".$campo[1]."','geral','".$this->phpSessaoId."');";
        $alt = "Procura CGM";
      break;
      case 'cgm-fisica':
        $func = "javascript:procurarCgm('".$this->form['name']."','".$campo[0]."','".$campo[1]."','fisica','".$this->phpSessaoId."');";
        $alt = "Procura Pessoa Física";
      break;
      case 'cgm-juridica':
        $func = "javascript:procurarCgm('".$this->form['name']."','".$campo[0]."','".$campo[1]."','juridica','".$this->phpSessaoId."');";
        $alt = "Procura Pessoa Jurídica";
      break;
      case 'funcionario':
        $func = "javascript:procurarCgm('".$this->form['name']."','".$campo[0]."','".$campo[1]."','funcionario','".$this->phpSessaoId."');";
        $alt = "Procura Funcionário";
      break;
      case 'usuario':
        $func = "javascript:procurarCgm('".$this->form['name']."','".$campo[0]."','".$campo[1]."','usuario','".$this->phpSessaoId."');";
        $alt = "Procura Usuário";
      break;
      case 'setor':
        $func = "javascript:procuraSetor('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$campo[2]."','".$this->phpSessaoId."');";
        $alt = "Procura Setor";
      break;
      case 'local':
        $func = "javascript:procuraLocal('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$this->phpSessaoId."');";
        $alt = "Procura Local";
      break;
      case 'data':
        $func = "javascript:MostraCalendario('".$this->form['name']."','".$campo[0]."','".$this->phpSessaoId."');";
        $alt = "Seleciona Data";
        $img = "calendario.gif";
      break;
      case 'uf':
        $func = "javascript:MostraEstados('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$campo[2]."','".$campo[3]."','".$this->phpSessaoId."');";
        $alt = "Procura Estado";
      break;
      case 'bem':
        $func = "javascript:procuraBem('".$this->form['name']."','".$campo[0]."','".$this->phpSessaoId."');";
        $alt = "Procura Bem";
      break;
      case 'veiculo':
        $func = "javascript:procuraVeiculo('".$this->form['name']."','".$campo[0]."','".$this->phpSessaoId."');";
        $alt = "Procura Veículo";
      break;
      case 'motorista':
        $func = "javascript:procuraMotorista('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$this->phpSessaoId."');";
        $alt = "Procura Motorista";
      break;
      case 'processo':
        $func = "javascript:procuraProcesso('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$this->phpSessaoId."');";
        $alt = "Procura Processo";
      break;
      case 'despesa':
        $func = "javascript:procuraContaDespesa('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$this->phpSessaoId."');";
        $alt = "Procura Conta de Despesa";
      break;
      case 'receita':
        //Neste caso o quarto campo deve ser 0 ou 1
        $func = "javascript:procuraContaReceita('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$campo[2]."','".$campo[3]."','".$this->phpSessaoId."');";
        $alt = "Procura Conta de Receita";
      break;
      case 'plano-contas':
        //Neste caso o quarto campo deve ser 0 ou 1
        $func = "javascript:procuraPlanoConta('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$campo[2]."','".$campo[3]."','".$this->phpSessaoId."');";
        $alt = "Procura Plano de Contas";
      break;
      case 'programa-trabalho':
        $func = "javascript:procuraProgramaTrabalho('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$campo[2]."','".$this->phpSessaoId."');";
        $alt = "Procura Programa de Trabalho";
      break;
      case 'autorizacao-empenho':
        $func = "javascript:procuraAutorizacaoEmpenho('".$this->form['name']."','".$campo[0]."','".$campo[1]."','".$this->phpSessaoId."');";
        $alt = "Procura Autorização";
      break;
      case 'empenho':
        $func = "javascript:procuraEmpenho('".$this->form['name']."','".$campo[0]."','".$this->phpSessaoId."');";
        $alt = "Procura Empenho";
      break;

      //Caso não se aplique nenhuma das opções acima utiliza a própria variável "$tipoBusca"
      default:
        $func = $tipoBusca;
      break;
    }

    //Cria a tag anchor <a> com a função escolhida
    $link .= '<a href="'.$func.'" '.$complemento.' tabindex="1" >';

    //Se o title ("hint") não foi definido utiliza o padrão de cada tipo de busca
    if ($title == "") {
        $title = $alt;
    }

    //Se a imagem não estiver definida, carrega a imagem padrão
    if ($imagem == "") {
        $imagem = $img;
    }

    //Insere a imagem
    $link .= "<img src='../../images/".$imagem."' title='".$title."' width='".$imgWidth."' height='".$imgHeight."' border='0'>";
    $link .= "</a>";

    //Verifica qual o último input inserido no campo atual
    $ultimoInput = count($this->campos[$this->chave]['input']);
    //Grava o link no último input inserido no campo atual
    $this->campos[$this->chave]['input'][$ultimoInput]['link'] = $link;
    //Outra opção de retorno para obter apenas o código html do link
    $this->aux = $link;
}

/**************************************************************************
 Método para inserir um livremente códigos html no lugar do input
**************************************************************************/
function criaInputGenerico($codigoHtml,$label)
{
    //Cria os dados para informações do campo
    $campo = array();
    $campo['label'] = $label;

    //Cria o item de formulário do campo
    $input = array();
    $input['tipoInput'] = "generico";
    $input['codHtml'] = $codigoHtml;

    $campo['input'][] = $input;

    $this->gravaCampo($campo);
}

/**************************************************************************
 Método para inserir um livremente códigos html dentro da tabela
**************************************************************************/
function criaCampoGenerico($codigoHtml)
{
    $campo = array();
    $campo['codHtml'] = $codigoHtml;
    $campo['generico'] = true;

    $this->gravaCampo($campo);
}

/**************************************************************************
 Método para criar uma combo a partir de um vetor, ou simplesmente vazia
**************************************************************************/
function criaComboVetor($name,$label="",$complemeto="",$obrigatorio=False,
         $valorPadrao="",$vetor="",$bMostraX=True,$sNomeX="Selecione",$dica="") {
    //Cria os dados para informações do campo
    $campo = array();
    $campo['label'] = $label;
    $campo['obrigatorio'] = $obrigatorio;
    $campo['dica'] = $dica;

    //Cria o item de formulário do campo
    $input = array();
    $input['tipoInput'] = "combo";
    $input['obrigatorio'] = $obrigatorio;
    $input['complemento'] = $complemento;
    $input['atributos'] = array('type'=>'combo','name'=>$name);

    $combo = '<select name="'.$name.'" style="width: 200px;" '.$complemento.' >';
    if ($bMostraX) {
        $combo .= '<option value="XXX" style="color:#ff0000">'.$sNomeX.'</option>'." \n";
    }

    //Grava os campos do vetor como variáveis
    if (is_array($vetor)) {
        foreach ($vetor as $chave=>$valor) {
            if ($chave == $valorPadrao) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $combo .= '<option '.$selected.' value="'.$chave.'">'.$valor.'</option>'."\n";
        }
    }
    $combo .= "</select> \n";

    $input['codHtml'] = $combo;

    $campo['input'][] = $input;

    $this->gravaCampo($campo);
}

/**************************************************************************/
/**** Retorna um combo de qualquer tabela ou query                      ***/
/**** Autor: Jorge Ribarr                                               ***/
/**************************************************************************/
function criaCombo($label, $obrigatorio, $sCampo, $sTabela, $sChave, $sTitulo,
                   $sFiltro, $sComplemento="", $sWhere="", $bMostraX=False,
                   $bOrderDesc=false,$bRetornaChaveTitulo=false,
                   $query="",$sNomeX="Selecione",$dica="") {
    //Cria os dados para informações do campo
    $campo = array();
    $campo['label'] = $label;
    $campo['obrigatorio'] = $obrigatorio;
    $campo['dica'] = $dica;

    //Cria o item de formulário do campo
    $input = array();
    $input['tipoInput'] = "combo";
    $input['obrigatorio'] = $obrigatorio;
    $input['complemento'] = $complemento;
    $input['atributos'] = array('type'=>'combo','name'=>$sCampo);

    $sCombo = "";
    $sFiltro = trim($sFiltro);
    if ($bOrderDesc==false) {
        $sOrder = $sTitulo;
    } else {
        $sOrder = $sTitulo." desc";
    }
    $dbCGen = new dataBase;
    $dbCGen->abreBD();
    if (strlen($query) == 0) {
        $sSQL = "select $sChave, $sTitulo from $sTabela $sWhere order by $sOrder";
    } else {
        $sSQL = $query;
    }
    $dbCGen->abreSelecao($sSQL);
    $dbCGen->fechaBD();
    $dbCGen->vaiPrimeiro();
    $sCombo .= '
    <select name="'.$sCampo.'" '.$sComplemento.'>';
    if ($bMostraX) {
        $sCombo .= '
        <option value="XXX" style="color:#ff0000">'.$sNomeX.'</option>';
    }
    while (!$dbCGen->eof()) {
        $sCha    = trim((string) $dbCGen->pegaCampo($sChave));
        if ($sCha==$sFiltro) {
            $sAux = "selected";
        } else {
            $sAux = "";
        }
        $aTitulos = explode(",",$sTitulo);
        $sTit = "";
        while (list ($key, $val) = each ($aTitulos)) {
            if (strlen($sTit)>0) {
                $sTit = $sTit." - ";
            }
            $sTit = $sTit.trim($dbCGen->pegaCampo(trim($val)));
        }
        if (!$bRetornaChaveTitulo) {
            $sCombo .= '
            <option '.$sAux.' value="'.$sCha.'">'.$sTit.'</option>';
        } else {
            $sCombo .= '
            <option '.$sAux.' value="'.$sCha.','.$sTit.'">'.$sTit.'</option>';
        }
        $dbCGen->vaiProximo();
    }
    $sCombo .= '
    </select>';
    $dbCGen->limpaSelecao();

    $input['codHtml'] = $sCombo;

    $campo['input'][] = $input;

    $this->gravaCampo($campo);
}

/**************************************************************************
 Método que cria um campo com input do tipo "textarea"
***************************************************************************/
function criaTextArea($name,$label="",$value="",$cols="40",$rows="4",$maxlength=0,
                      $obrigatorio=0,$complemento="",$dica="") {
    //Cria os dados para informações do campo
    $campo = array();
    $campo['label'] = $label;
    $campo['obrigatorio'] = $obrigatorio;
    $campo['dica'] = $dica;
    $campo['complementoLabel'] = ' style="vertical-align: top;" ';

    //Cria o item de formulário do campo
    $input = array();
    $input['tipoInput'] = "textarea";
    $input['obrigatorio'] = $obrigatorio;
    $input['complemento'] = $complemento;
    $input['value'] = $value;

    //Cria um vetor para os atributos
    $atr = array();
    $atr['name'] = $name;
    $atr['cols'] = $cols;
    $atr['rows'] = $rows;
    if ($maxlength > 0) {
        $atr['onKeyPress'] = 'return(maxTextArea(this.form.'.$name.','.$maxlength.',event));';
        $atr['onBlur'] = 'return(maxTextArea(this.form.'.$name.','.$maxlength.',event,true));';
    }

    $input['atributos'] = $atr;

    $campo['input'][] = $input;

    $this->gravaCampo($campo);
}

/**************************************************************************
 Método que cria um campo com input do tipo "checkbox"
 O parâmetro "$repete" indica se o input pertence a um input já declarado,
 evitanto chamar o método "agregaCampo()"
***************************************************************************/
function criaCheckBox($name,$label="",$value="",$nome="",$checked=False,$repete=False,$complemento="",$dica="")
{
    //Cria os dados para informações do campo
    $campo = array();
    $campo['label'] = $label;
    $campo['dica'] = $dica;
    //$campo['obrigatorio'] = $obrigatorio;

    //Cria o item de formulário do campo
    $input = array();
    $input['tipoInput'] = "checkbox";
    //$input['obrigatorio'] = $obrigatorio;
    $input['complemento'] = $complemento;
    $input['nome'] = $nome;

    //Cria um vetor para os atributos
    $atr = array();
    $atr['name'] = $name;
    $atr['value'] = $value;
    if ($checked) {
        $atr['checked'] = '';
    }

    $input['atributos'] = $atr;

    $campo['input'][] = $input;

    if ($repete) {
        $this->agregaCampo();
    }

    $this->gravaCampo($campo);
}

/**************************************************************************
 Método que cria um campo com input do tipo "radio"
 O parâmetro "$repete" indica se o input pertence a um input já declarado,
 evitanto chamar o método "agregaCampo()"
***************************************************************************/
function criaRadio($name,$label="",$value="",$nome="",$checked=False,$repete=False,$complemento="",$dica="")
{
    //Cria os dados para informações do campo
    $campo = array();
    $campo['label'] = $label;
    $campo['dica'] = $dica;

    //Cria o item de formulário do campo
    $input = array();
    $input['tipoInput'] = "radio";
    $input['complemento'] = $complemento;
    $input['nome'] = $nome;

    //Cria um vetor para os atributos
    $atr = array();
    $atr['name'] = $name;
    $atr['value'] = $value;
    if ($checked) {
        $atr['checked'] = '';
    }

    $input['atributos'] = $atr;

    $campo['input'][] = $input;

    if ($repete) {
        $this->agregaCampo();
    }

    $this->gravaCampo($campo);
}

/**************************************************************************
 Método que altera a flag "flagGravaCampo" para forçar um input a ser
 adicionado a um campo já existente
***************************************************************************/
function agregaCampo()
{
    //Altera a flag para não gerar um novo campo
    $this->flagGravaCampo = false;
}

/**************************************************************************
 Método que grava um vetor na variável de classe que recebe os campos
 Registra a chave em que foi gravada no vetor para permitir alterações
***************************************************************************/
function gravaCampo($vetor)
{
    if ($this->flagGravaCampo) {
        $quant = count($this->campos);
        $ok = true;
        while ($ok) {
            if (!array_key_exists($quant,$this->campos)) {
                $this->campos[$quant] = $vetor;
                $ok = false;
            } else {
                $quant++;
            }
        }

        //Grava o estilo de tabela que estiver configurado no momento
        $this->campos[$quant]['estiloLabel'] = $this->tabela['estiloLabel'];
        $this->campos[$quant]['estiloField'] = $this->tabela['estiloField'];
        $this->campos[$quant]['widthLabel'] = $this->tabela['widthLabel'];
        $this->campos[$quant]['widthField'] = $this->tabela['widthField'];

        //Retorna a chave em que foi gravado o vetor na variável de sessão $chave
        //Caso seja necessário saber qual o último campo gerado, basta chamar $this->campos[$this->chave]
        $this->chave = $quant;
    } else { //Grava o novo input dentro de um campo existente
        //Retorna a flag para seu valor padrão
        $this->flagGravaCampo = true;

        //Verifica se já foi criado ao menos um campo
        if (count($this->campos[$this->chave]) > 0) {
            $this->campos[$this->chave]['input'][] = $vetor['input'][0];
        }
    }
}

/**************************************************************************
 Método que gera os campos ocultos do formulário
***************************************************************************/
function geraCamposOcultos()
{
    $html = "";
    foreach ($this->camposOcultos as $fld) {
        $html .= '<input type="hidden" name="'.$fld[name].'" value="'.$fld[value].'">';
    }

    $this->html .= $html;
}

/**************************************************************************
 Método que cria um novo campo do tipo hidden
***************************************************************************/
function criaCampoOculto($name,$value)
{
    $vet = array();
    $vet['name'] = $name;
    $vet['value'] = $value;
    $this->camposOcultos[] = $vet;
}

/**************************************************************************
 Método para criar abas no topo de um formulário.
 O parâmetro "$aAbas" dever ser uma string separada por vírgulas
 É necessário que o campo "controle" exista como hidden
 Obs: Útil para formulários com grande quantidade de dados
***************************************************************************/
function criaAbas($aAbas,$abaAtual=0,$abaAnterior=0,$width="100%")
{
    $html = "";
    $aba = explode(",",$aAbas);

    //Gera uma tabela fora da tabela de formulários
    $html .= "<table width='".$width."' cellspacing='1' cellpadding='4'><tr>";

    foreach ($aba as $chave=>$val) {
        if ($abaAtual == $chave) {
            $estilo = "show_dados";
        } else {
            $estilo = "labelleft";
        }

        $html .= "<td class=".$estilo." >";
        $html .= "<a href='javascript:mudarAba(".$abaAtual.",".$chave.");'>".$val." ";
        $html .= "</a></td>";
    }

    $html .= "</tr></table>";

    //Grava o código em variável de sessão
    $this->abas .= $html;
}

/**************************************************************************
 Método que gera scripts para a validação do formulário
***************************************************************************/
function geraJavaScript()
{
    $js = "<script type='text/javascript'> \n";
    //Permite incluir outras funções javascript
    $js .= $this->sJava." \n ";
    //Função para trocar de aba -- É necessário que o campo "controle" exista
    $js .= 'function mudarAba(keyant,key) {
                var f = document.'.$this->form['name'].';
                var cont = f.controle.value;
                cont = cont - 1;
                f.controle.value = 0;
                f.target = "telaPrincipal";
                f.action = "'.$this->arquivo.'?'.$this->phpSessaoId.'&aba="+key+"&abaAnt="+keyant;
                f.submit();
                f.controle.value = cont + 1; //Retorna o input controle para seu valor original após o submit
            }';
    //Cria a função JS para enviar o formulário para o frame oculto. É necessário que o campo "controle" exista
    $js .= "//Faz o submit do formulário sem sair da página atual, envia para o frame oculto
            function validacao(cod)
            {
                var f = document.".$this->form['name'].";
                var cont = f.controle.value;
                f.target = 'oculto';
                f.controle.value = cod; //Indica qual ação deve ser executada para validar o campo
                f.submit();
                f.controle.value = cont; //Retorna o input controle para seu valor original após o submit
            } \n";
    //Cria uma função que simula o comportamento da função reset, colocando valor nulo para todos os campos
    $js .= "function limpa() {
                var f = document.".$this->form['name'].";
                var campo;
                var aux; \n";
    foreach ($this->campos as $campo) {
        if (array_key_exists('generico',$campo)) {
            continue;
        } else {
            $aux = 0;
            foreach ($campo['input'] as $input) {
                switch ($input['tipoInput']) {
                    case "texto":
                    $js .= "f.".$input['atributos']['name'].".value = ''; ";
                    break;
                    case "textarea":
                    $js .= "f.".$input['atributos']['name'].".value = ''; ";
                    break;
                    case "combo":
                    $js .= "f.".$input['atributos']['name'].".options[0].selected = true; ";
                    break;
                    case "radio":
                    $js .= "f.".$input['atributos']['name']."[0].checked = true; ";
                    break;
                    case "checkbox":
                    $js .= "f.".$input['atributos']['name']."[".$aux."].checked = false; ";
                    $aux++;
                    break;
                }//fim switch
                $js .= "\n";
            }
        }
    }
    $js .= "} \n";

    //Função que verifica se os campos obrigatórios estão preenchidos
    $js .= "function Valida() {
                var mensagem = '';
                var erro = false;
                var campo;
                var campoaux;
                var f = document.".$this->form['name']."; \n";

    //Permite incluir validação manualmente
    $js .= $this->sJavaValida." \n ";

    if ($this->flagValidacao) {
        //Gera validacao para os campo obrigatórios de acordo com o tipo do campo
        foreach ($this->campos as $campo) {
        if (array_key_exists('generico',$campo)) {
            continue;
        } else {
            foreach ($campo['input'] as $input) {
            if ($input['obrigatorio']) {
                //switch ($input['atributos']['type']) {
                switch ($input['tipoInput']) {
                    case "texto":
                    $js .= "campo = f.".$input['atributos']['name'].".value.length;
                            if (campo==0) {
                                mensagem += '@O Campo ".$campo['label']." é Obrigatório';
                                erro = true;
                            } ";
                    break;
                    case "combo":
                    $js .= "campo = f.".$input['atributos']['name'].".value;
                            if (campo=='XXX') {
                                mensagem += '@O Campo ".$campo['label']." é Obrigatório';
                                erro = true;
                            } ";
                    break;
                    case 'textarea':
                    $js .= "campo = f.".$input['atributos']['name'].".value.length;
                            if (campo==0) {
                                mensagem += '@O Campo ".$campo['label']." é Obrigatório';
                                erro = true;
                            } ";
                    break;
                }//fim switch
                $js .= "\n";
            }//fim if
            }//fim foreach
        }//fim if
        }//fim foreach
    }//fim if($this->flagValidacao)

    $js .= "if (erro) alertaAviso(mensagem,'form','erro','".$this->phpSessaoId."');

            return !(erro);
        }// Fim da function Valida

        //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
        function Salvar()
        {
            var f = document.".$this->form['name'].";
            f.ok.disabled = true;
            if (Valida()) {
                f.submit();
            } else {
                f.ok.disabled = false;
            }
        }
    </script> \n";

    return $js;
}//Fim da function geraJavaScript

/**************************************************************************
 Método que gera o botão ok, limpar e o aviso de campos obrigatórios
***************************************************************************/
function geraBotaoOk()
{
    $html = '<table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>';
    if ($this->flagSalvar) {
        $html .= '<input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();">';
    } else {
        $html .= "<input type='submit' name='ok' value='OK' style='width: 60px;'>";
    }
    if ($this->flagBotaoLimpar) {
      if ($this->flagReset) {
        $html .= '&nbsp;<input type="reset" name="limpar" value="Limpar" style="width: 60px">';
      } else {
        $html .= '&nbsp;<input type="button" name="limpar" value="Limpar" style="width: 60px" onClick="limpa();">';
      }
    }
    $html .= '</td><td class="fieldright_noborder">';
    if ($this->flagCamposObrigatorios) {
        $html .= '<b>* Campos Obrigatórios</b>';
    }
    $html .= '</td></tr></table>';

    return $html;
}//Fim da function geraBotaoOk

}//Fim da classe

?>
