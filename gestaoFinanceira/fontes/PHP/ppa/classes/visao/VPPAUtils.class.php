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
 * Classe de utilitarios

 * Data de Criação   : 22/09/2008

 * @author Analista      : Bruno Ferreira
 * @author Desenvolvedor : Jânio Eduardo
 * @ignore

 * $Id: VPPAUtils.class.php 39644 2009-04-13 20:53:17Z pedro.medeiros $

 * Casos de uso:
 */

class VPPAUtils
{
    /**
     * Prepara código HTML para ser repassado como string Javascript.
     * @param $stHTML o código em HTML
     * @return string formatada para javascript
     */
    public function formataJavaScript($stHTML)
    {
        # Substitui elementos em javascript que causam erro de parsing.
        $stHTML = str_replace("\n", '', $stHTML);
        $stHTML = str_replace('  ', '', $stHTML);
        $stHTML = str_replace('\'', '\\\'', $stHTML);

        return $stHTML;
    }

    /**
     * Similar ao método montaLista, mas este recebe os atributos e valores da
     * linha em um único array.
     * Adaptação do método montaLista para atender as necessidades do módulo Receita.
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     *
     * @param string $stNome           nome do elemento listado
     * @param string $stTitulo         título da lista
     * @param array  $arCabecalhos     cabeçalhos da lista
     * @param array  $arValoresLinha   Valores e compnentes da lista que constituem uma coluna
     * @param bool   $boConsulta=false se lista serve apenas para consulta (sem ação)
     * @param bool   $boJS=true        se a função gera lista para javascript ou HTML puro
     *
     * @return string código HTML da Lista
     */
    public function montaListaMixed($stNome, $stTitulo, $arCabecalhos, $arValoresLinha, $boConsulta = false, $boJS = true)
    {

        $rsValores = new RecordSet();
        $rsValores->preenche($arValoresLinha);

        $obLista = new Lista();
        $obLista->setMostraPaginacao(false);
        $obLista->setTitulo($stTitulo);
        $obLista->setRecordSet($rsValores);
        $obLista->setNumeracao(false);

        # Monta cabeçalho de número do elemento.
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(5);
        $obLista->commitCabecalho();

        # Monta outros cabeçalhos.
        $i=0;
        foreach ($arCabecalhos as $inKey => &$arDados) {

            $obLista->addCabecalho();
            if (isset($arDados[$inKey]['null']) && $arDados[$inKey]['null'] == false) {
                $arDados['cabecalho'] = '*' . $arDados['cabecalho'];
            }
            $obLista->ultimoCabecalho->addConteudo($arDados['cabecalho']);
            $obLista->ultimoCabecalho->setWidth($arDados['width']);
            $obLista->commitCabecalho();
            $i++;
        }

        # Verifica se é uma consulta
        if (!$boConsulta) {
            # Monta cabeçalho de ação.
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth(1);
            $obLista->commitCabecalho();
        }

        $numLinhasLista = count($arValoresLinha);

        # Gera hidden com número de elementos.
        $obHiddenNumLinhas = new Hidden();
        $obHiddenNumLinhas->setName('inSize' . $stNome);
        $obHiddenNumLinhas->setValue($numLinhasLista);
        $obHiddenNumLinhas->setID('inSize' . $stNome);
        $obHiddenNumLinhas->montaHtml();
        $stHTML =  $obHiddenNumLinhas->getHtml();

        # Monta dados.

        for ($inLinha = 0; $inLinha < $numLinhasLista; $inLinha++) { // LINHAS
            $obLista->addLinha();

            $obLista->ultimaLinha->addCelula();
            $obLista->ultimaLinha->ultimaCelula->addConteudo($inLinha + 1);
            $obLista->ultimaLinha->ultimaCelula->setClass('labelCenter');
            $obLista->ultimaLinha->commitCelula();

            $numColunasLinha = count($rsValores->arElementos[0]);

            $tipoCampo = strtolower($arValoresLinha[$inLinha]['tipo']);

            for ($inColuna = 0; $inColuna < $numColunasLinha; $inColuna++) { // COLUNAS
                $obHtml = ucfirst($arValoresLinha[$inLinha][$inColuna]['tipo']);
                $tipoCampo = strtolower($arValoresLinha[$inLinha][$inColuna]['tipo']);

                if ($tipoCampo == 'hidden') {
                    $fieldName = $arValoresLinha[$inLinha][$inColuna]['name'].'['.$inLinha.']';
                    $obHiddenKey = $this->_geraHidden($fieldName, $arValoresLinha[$inLinha][$inColuna]['value'], $fieldName);
                    $name = $arValoresLinha[$inLinha][$inColuna]['name'];
                    $obHiddens[] = $obHiddenKey;
                    continue;

                }
                $obComponente = new $obHtml;
                // ID
                if (isset($arValoresLinha[$inLinha][$inColuna]['id'])) {
                    $obComponente->setID($arValoresLinha[$inLinha][$inColuna]['id']);
                } else {
                    $obComponente->setID($arValoresLinha[$inLinha][$inColuna]['name'] . '_' . $inLinha);
                }
                // name
                if (isset($arValoresLinha[$inLinha][$inColuna]['name'])) {
                    $obComponente->setName($arValoresLinha[$inLinha][$inColuna]['name'].'['.$inLinha.']');
                }
                // setValue
                if (empty($arValoresLinha[$inLinha][$inColuna]['value'])) {
                    $obComponente->setValue('&nbsp;');
                } else {
                    $obComponente->setValue($arValoresLinha[$inLinha][$inColuna]['value']);
                }
                // setNull
                if (isset($arValoresLinha[$inLinha][$inColuna]['null'])) {
                    $obComponente->setNull($arValoresLinha[$inLinha][$inColuna]['null']);
                }
                // setOnClick
                if (isset($arValoresLinha[$inLinha][$inColuna]['onClick'])) {
                    $obComponente->obEvento->setOnClick($arValoresLinha[$inLinha][$inColuna]['onClick']);
                }
                // setOnChange
                if (isset($arValoresLinha[$inLinha][$inColuna]['onChange'])) {
                    $obComponente->obEvento->setOnChange($arValoresLinha[$inLinha][$inColuna]['onChange']);
                }
                // setOnBlur
                if (isset($arValoresLinha[$inLinha][$inColuna]['onBlur'])) {
                    $obComponente->obEvento->setOnBlur($arValoresLinha[$inLinha][$inColuna]['onBlur']);
                }
                // maxlength
                if (method_exists($obComponente,'setMaxLength')) {
                    if (isset($arValoresLinha[$inLinha][$inColuna]['maxlength'])) {
                        $obComponente->setMaxLength($arValoresLinha[$inLinha][$inColuna]['maxlength']);
                    } else {
                        $obComponente->setMaxLength(10);
                    }
                }
                // size
                if (method_exists($obComponente,'setSize')) {
                    $obComponente->setSize(14);
                }
                if (isset($arValoresLinha[$inLinha][$inColuna]['size'])) {
                    $obComponente->setSize($arValoresLinha[$inLinha][$inColuna]['size']);
                }
                // setReadOnly
                if (isset($arValoresLinha[$inLinha][$inColuna]['readOnly'])) {
                    $obComponente->setReadinColunaOnly($arValoresLinha[$inLinha][$inColuna]['readOnly']);
                }

                $obLista->ultimaLinha->addCelula();
                $obLista->ultimaLinha->ultimaCelula->addComponente($obComponente);
                $obLista->ultimaLinha->ultimaCelula->setClass('field');
                $obLista->ultimaLinha->commitCelula();

            }

            # Verifica se é consulta
            if (!$boConsulta) {
                # Monta ação de exclusão na última coluna.
                $obAcao = new Acao();
                $obAcao->setAcao('EXCLUIR');
                $obAcao->setFuncaoAjax(true);
                $obAcao->obBotao->obEvento->setOnClick('excluir' . $stNome . '(this);');

                $obLista->ultimaLinha->addCelula();
                $obLista->ultimaLinha->ultimaCelula->addComponente($obAcao);

                foreach ($obHiddens as $value) {
                    $obLista->ultimaLinha->ultimaCelula->addComponente($value);
                }

                $obHiddens = null;
                $obLista->ultimaLinha->ultimaCelula->setClass('field');
                $obLista->ultimaLinha->commitCelula();
            }
            $obLista->commitLinha();
        }

        $obLista->montaHTML();

        # Manda o retorno como javascript ou não.
        if ($boJS) {
            return $this->formataJavaScript($stHTML . $obLista->getHTML());
        }

        return $stHTML . $obLista->getHTML();
    }

    /**
     * Monta lista dinâmica em 3 arrays distintos de cabeçalho, componentes e valores.
     *
     * @param  string $stNome           nome do elemento listado
     * @param  string $stTitulo         título da lista
     * @param  array  $arCabecalhos     cabeçalhos da lista
     * @param  array  $arComponentes    componentes da lista que constituem uma coluna
     * @param  array  $arValores        valores a serem preenchidos na lista
     * @param  bool   $boConsulta=false se lista serve apenas para consulta (sem ação)
     * @param  bool   $boJS=true        se a função gera lista para javascript ou HTML puro
     * @param  bool   $boNumeracao=true se a função gera lista com numeração na primeira coluna
     * @return string código HTML da Lista
     */
    public function montaLista($stNome, $stTitulo, $arCabecalhos, $arComponentes, $arValores, $boConsulta = false, $boJS = true, $boNumeracao = true)
    {
        $rsValores = new RecordSet();
        $rsValores->preenche($arValores);

        $obLista = new Lista();
        $obLista->setMostraPaginacao(false);
        $obLista->setTitulo($stTitulo);
        $obLista->setRecordSet($rsValores);

        # Monta cabeçalho de número do elemento.
        if ($boNumeracao) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth(5);
            $obLista->commitCabecalho();
        }

        # Monta outros cabeçalhos.
        foreach ($arCabecalhos as $inKey => &$arDados) {
            # Coluna vazia, pular.
            if ($arDados[0] == '-') {
                continue;
            }

            $obLista->addCabecalho();

            if (isset($arComponentes[$inKey]['null']) && $arComponentes[$inKey]['null'] === false) {
                $arDados['cabecalho'] = '*' . $arDados['cabecalho'];
            }

            $obLista->ultimoCabecalho->addConteudo($arDados['cabecalho']);
            $obLista->ultimoCabecalho->setWidth($arDados['width']);
            $obLista->commitCabecalho();
        }

        # Verifica se é uma consulta
        if (!$boConsulta) {
            # Monta cabeçalho de ação.
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth(1);
            $obLista->commitCabecalho();
        }

        # Gera hidden com número de elementos.
        $obHidden = new Hidden();
        $obHidden->setName('inSize' . $stNome);
        $obHidden->setValue(count($arValores));
        $obHidden->montaHtml();

        $stHTML =  $obHidden->getHtml();

        # Monta dados.
        foreach ($arValores as $inLinha => $arCampos) {
            $obLista->addLinha();

            if ($boNumeracao) {
                $obLista->ultimaLinha->addCelula();
                $obLista->ultimaLinha->ultimaCelula->addConteudo($inLinha + 1);
                $obLista->ultimaLinha->ultimaCelula->setClass('labelCenter');
                $obLista->ultimaLinha->commitCelula();
            }

            foreach ($arComponentes as &$arDados) {

                $obComponente = new $arDados['tipo']();

                $obComponente->setID($arDados['name'] . '_' . $inLinha);

                # Objeto do tipo hidden não usa os outros atributos abaixo.
                if (strcasecmp($arDados['tipo'], 'Hidden') == 0) {
                    $stHTML .= $this->geraHidden($arDados['name'], $arCampos[$arDados['campo']]);
                    continue;
                }

                if (strcasecmp($arDados['tipo'], 'Label') == 0) {
                    $stHTML .= $this->geraHidden($arDados['name'], $arCampos[$arDados['campo']]);
                }
                if ($arDados['name']) {
                    $obComponente->setName($arDados['name'].'['.$inLinha.']');
                }
                if ($arDados['selecione']) {
                    $obComponente->addOption("", "Selecione");
                }
                if ($arDados['campoId']) {
                    $obComponente->setCampoID($arDados['campoId']);
                }
                if ($arDados['campoDesc']) {
                    $obComponente->setCampoDesc($arDados['campoDesc']);
                }
                if ($arDados['preenche']) {
                    $obComponente->preencheCombo($arDados['preenche']);
                }
                if ($arDados['campo']) {
                    $obComponente->setValue(stripslashes($arCampos[$arDados['campo']]));
                }
                if ($arDados['null']) {
                    $obComponente->setNull($arDados['null']);
                }
                if ($arDados['onClick']) {
                    $obComponente->obEvento->setOnClick($arDados['onClick']);
                }
                if ($arDados['onChange']) {
                    $obComponente->obEvento->setOnChange($arDados['onChange']);
                }
                if ($arDados['onBlur']) {
                    $obComponente->obEvento->setOnBlur($arDados['onBlur']);
                }
                if ($arDados['size']) {
                    $obComponente->setSize($arDados['size']);
                }
                if ($arDados['readOnly']) {
                    $obComponente->setReadOnly($arDados['readOnly']);
                }
                if ($arDados['setMaxLength']) {
                    $obComponente->setMaxLength($arDados['setMaxLength']);
                }
                if ($arDados['style']) {
                    $obComponente->setStyle($arDados['style']);
                }
                if ($arDados['decimais']) {
                    $obComponente->setDecimais($arDados['decimais']);
                }

                $obLista->ultimaLinha->addCelula();
                $obLista->ultimaLinha->ultimaCelula->addComponente($obComponente);
                $obLista->ultimaLinha->ultimaCelula->setClass('field');

                # Ajusta alinhamento do componente.
                switch ($arDados['alinhamento']) {
                case 'CENTRO':
                    $obLista->ultimaLinha->ultimaCelula->setClass('fieldCenter');
                    break;

                case 'ESQUERDA':
                    $obLista->ultimaLinha->ultimaCelula->setClass('fieldleft');
                    break;

                case 'DIREITA':
                    $obLista->ultimaLinha->ultimaCelula->setClass('fieldright');
                    break;

                default:
                }

                $obLista->ultimaLinha->commitCelula();
            }

            # Verifica se é consulta
            if (!$boConsulta) {
                # Monta ação de exclusão na última coluna.
                $obAcao = new Acao();
                $obAcao->setAcao('EXCLUIR');
                $obAcao->setFuncaoAjax(true);
                $obAcao->obBotao->obEvento->setOnClick('excluir' . $stNome . '(this);');

                $obLista->ultimaLinha->addCelula();
                $obLista->ultimaLinha->ultimaCelula->addComponente($obAcao);
                $obLista->ultimaLinha->ultimaCelula->setClass('field');
                $obLista->ultimaLinha->commitCelula();
            }
            $obLista->commitLinha();
        }

        $obLista->montaHTML(true);

        # Manda o retorno como javascript ou não.
        if ($boJS) {
            return $this->formataJavaScript($stHTML . $obLista->getHTML());
        }

        return $stHTML . $obLista->getHTML();
    }

    //monta lista dinamica
    public function montaListaTextBox($cabecalho,$campo,$arValores)
    {
        if (is_array($arValores)) {
            $rsRecordSet = new RecordSet;
            $rsRecordSet->preenche($arValores);
            $obLista = new Lista;
            $obLista->setMostraPaginacao(false);
            $obLista->setTitulo($cabecalho['cabecalho']);
            $obLista->setRecordSet($rsRecordSet);

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth(5);
            $obLista->commitCabecalho();

            # Monta subcabeçalho
            foreach ($campo as $valueCampo) {
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo($valueCampo);
                $obLista->ultimoCabecalho->setWidth(5);
                $obLista->commitCabecalho();
            }

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('Ação');
            $obLista->ultimoCabecalho->setWidth(10);
            $obLista->commitCabecalho();

            # Monta dados
            $linha = 1;

            foreach ($arValores as $key => $value) {
                $obLista->addLinha();

                $obNroLinha = new hidden;
                $obNroLinha->setName('nroLinha');
                $obNroLinha->setValue( $linha );

                $obLista->ultimaLinha->addCelula();
                $obLista->ultimaLinha->ultimaCelula->addComponente($obNroLinha);
                $obLista->ultimaLinha->ultimaCelula->addConteudo($linha);
                $obLista->ultimaLinha->ultimaCelula->setClass('labelCenter');
                $obLista->ultimaLinha->commitCelula();

                for ($x = 0; $x < count($value); $x++) {
                    $obTextBox = new TextBox;
                    $obTextBox->setName('linha['.$linha.'][]');
                    $obTextBox->setValue( $value[$x] );
                    $obTextBox->setSize(20);

                    $obLista->ultimaLinha->addCelula();
                    $obLista->ultimaLinha->ultimaCelula->addComponente($obTextBox);
                    $obLista->ultimaLinha->ultimaCelula->setClass('label');
                    $obLista->ultimaLinha->ultimaCelula->setAlign('right');
                    $obLista->ultimaLinha->commitCelula();
                }

                $obAcao = new acao;
                $obAcao->setAcao("excluir");
                $obAcao->obBotao->obEvento->setOnClick("excluirLinhaLista(this);");

                $obLista->ultimaLinha->addCelula();
                $obLista->ultimaLinha->ultimaCelula->addComponente($obAcao);
                $obLista->ultimaLinha->ultimaCelula->setClass('labelCenter');
                $obLista->ultimaLinha->commitCelula();
                $obLista->commitLinha();
                $linha++;
            }

            $obLista->montaHTML();
            $html = $obLista->getHTML();
            $html = str_replace( "\n","",$html   );
            $html = str_replace( "  ","",$html   );
            $html = str_replace( "'","\\'",$html );
        }

        $stJs.= " d.getElementById('{$cabecalho['span']}').innerHTML  = '".$html."'; \n";

        return $stJs;
    }

    /**
     * Gerador de item em array de hiddens
     * @param  string $nome  nome do elemento do array
     * @param  mixed  $valor o valor do elemento do array
     * @return string código HTML do hidden
     */
    public function geraHidden($nome, $valor)
    {
        $obHdn = new Hidden();
        $obHdn->setName("{$nome}[]");
        $obHdn->setId("{$nome}[]");
        $obHdn->setValue($valor);
        $obHdn->montaHtml();

        return $obHdn->getHtml();
    }

    /**
     * Gerador de item em array de hiddens
     * @param  string $nome  nome do elemento do array
     * @param  mixed  $valor o valor do elemento do array
     * @return string código HTML do hidden
     * @todo mesclar com função geraHidden
     */
    public function _geraHidden($nome, $valor, $id = null)
    {
        $obHdn = new Hidden();
        $obHdn->setName($nome);
        $obHdn->setValue($valor);
        if (isset($id)) {
            $obHdn->setID($id);
        }

        return $obHdn;
    }

    /**
     * Converte float em string contendo número formatado.
     * @param  float  $flNum o número<in
     * @return string string contendo o número formatado
     */
    public function floatToStr($flNum)
    {
        $boNeg = $flNum < 0 ? '-' : '';

        if ($boNeg) {
            $flNum = -$flNum;
        }

        $flNum = sprintf('%f', $flNum);

        # Obtém parte inteira e decimal do número.
        list($stInt, $stDec) = explode('.', $flNum);

        $stInt = $stInt ? $stInt : '0';
        $stDec = $stDec ? $stDec : '0';

        $arNum = array();
        $stNum = '';
        $j = 0;

        for ($i = strlen($stInt) - 1; $i >= 0; $i--) {
            $stNum = $stInt[$i] . $stNum;

            if ((++$j % 3 == 0) && $i) {
                $stNum = '.' . $stNum;
            }
        }

        return $boNeg . $stNum . ',' . substr($stDec . '00', 0, 2);
    }

    /**
     * Converte string com número formatado em tipo numérico.
     * @param  string $stNum string contendo número formatado
     * @return float  valor convertido em float
     */
    public function strToFloat($stNum)
    {
        $stNum = str_replace('.', '', $stNum);
        $stNum = str_replace(',', '.', $stNum);

        return (float) $stNum;
    }

    /**
     * Retira acentuação de uma string.
     *
     * @param string
     * @return string
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     * @ignore Este método pode ser reescrito para utilizar regex
     */
    public function retirarAcento($string)
    {
        $char = array("ç" => "c", "Ç" => "C", "à" => "a", "À" => "A",
                      "á" => "a", "Á" => "A", "ã" => "a", "Ã" => "A",
                      "â" => "a", "Â" => "A", "è" => "e", "È" => "E",
                      "é" => "e", "É" => "E", "ê" => "e", "Ê" => "E",
                       "ì" => "i", "Ì" => "I", "í" => "i", "Í" => "I",
                      "î" => "i", "Î" => "I", "ò" => "o", "Ò" => "O",
                      "ó" => "o", "Ó" => "O", "õ" => "o", "Õ" => "O",
                      "ô" => "o", "Ô" => "O", "ù" => "u", "Ù" => "U",
                      "ú" => "u", "Ú" => "U", "û" => "u", "Û" => "U");

        $strTratada = strtr($string, $char);

        return $strTratada;
    }

    public function arredondaValorMonetario($flValor)
    {
        if (!is_numeric($flValor)) {
            $flValor = str_replace(',', '.', str_replace('.', '', $flValor));
        }

        $arValor = explode('.', $flValor);
        $inCount = strlen($arValor[1]);
        $inParametro = str_pad(6, $inCount, 5, STR_PAD_LEFT);

        $flReturn = $arValor[0];

        if ($arValor[1] >= $inParametro) {
            $flReturn = $arValor[0] + 1;
        }

        return $flReturn;
    }

}

?>
