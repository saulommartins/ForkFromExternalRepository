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
 * Classe Lista Util
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Janio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage LDO
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

class LDOLista
{
    /**
     * Prepara código HTML para ser repassado como string Javascript.
     * @param $stHTML o código em HTML
     * @return string formatada para javascript
     */
    public static function formatarJavaScript($stHTML)
    {
        # Substitui elementos em javascript que causam erro de parsing.
        $stHTML = str_replace("\n", '', $stHTML);
        $stHTML = str_replace('  ', '', $stHTML);
        $stHTML = str_replace('\'', '\\\'', $stHTML);

        return $stHTML;
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
     * @param  bool   $stFuncaoExclusao nome da função que exclui da lista, se nulo, usa a função excluir + $stNome
     * @return string código HTML da Lista
     */
    public static function montarLista($stNome, $stTitulo, $arCabecalhos, $arComponentes, $arValores, $boConsulta = false, $boJS = true, $boNumeracao = true, $stFuncaoExclusao = null)
    {
        $rsValores = new RecordSet();
        $rsValores->preenche($arValores);

        $obLista = new Lista();
        $obLista->setMostraPaginacao(false);
        $obLista->setTitulo($stTitulo);
        $obLista->setRecordSet($rsValores);
        $obLista->setID('obTbl' . $stNome);

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

                $obLista->ultimaLinha->addCelula();

                # Objeto do tipo hidden não usa os outros atributos abaixo.
                if (strcasecmp($arDados['tipo'], 'Hidden') == 0) {
                    $obHdn = self::gerarHidden($arDados['name'], $arCampos[$arDados['campo']]);
                    $obHdn->montaHTML();
                    $stHTML.= $obHdn->getHtml();
                    continue;
                }

                if (strcasecmp($arDados['tipo'], 'Label') == 0) {
                    $obHdn = self::gerarHidden($arDados['name'], $arCampos[$arDados['campo']]);
                    $obLista->ultimaLinha->ultimaCelula->addComponente($obHdn);
                }
                if ($arDados['name']) {
                    $obComponente->setName($arDados['name'].'['.$inLinha.']');
                }
                if ($arDados['campo']) {
                    $obComponente->setValue($arCampos[$arDados['campo']]);
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

                if (!$stFuncaoExclusao) {
                    $obAcao->obBotao->obEvento->setOnClick('excluir' . $stNome . '(this);');
                } else {
                    $obAcao->obBotao->obEvento->setOnClick($stFuncaoExclusao . '(this);');
                }

                $obLista->ultimaLinha->addCelula();
                $obLista->ultimaLinha->ultimaCelula->addComponente($obAcao);
                $obLista->ultimaLinha->ultimaCelula->setClass('field');
                $obLista->ultimaLinha->commitCelula();
            }
            $obLista->commitLinha();
        }

        $obLista->montaHTML();

        # Manda o retorno como javascript ou não.
        if ($boJS) {
            return self::formatarJavaScript($stHTML . $obLista->getHTML());
        }

        return $stHTML . $obLista->getHTML();
    }

    /**
     * Monta lista utilizando objeto TableTree
     *
     * @param  string $stTitulo
     * @param  array  $arCabecalho
     * @param  array  $arCampos
     * @param  string $stScritConsulta   Método para popular o item filho ([+])
     * @param  array  $arParametrosLista
     * @return string HTML Lista
     */
    public static function montarListaTreeView($stTitulo, $arCabecalho, $arCampos, $stScritConsulta, $arParametros, $arDadosLista = null, $boJS = true)
    {
        $listaTreeView = new TableTree();
        if (!isset($arDadosLista)) {
            $arDadosLista = new RecordSet();
        }
        $listaTreeView->setRecordset  ( $arDadosLista );
        $listaTreeView->setSummary    ($stTitulo);
        $listaTreeView->setArquivo    ( $stScritConsulta );
        $listaTreeView->setParametros ( $arParametros );
       // $listaTreeView->setConditional( true , "#efefef" ); // lista zebrada

        foreach ($arCabecalho as $inKey => $arDados) {
            $listaTreeView->Head->addCabecalho ( $arDados['titulo'], $arDados['width']  );
        }

        foreach ($arCampos as $inKey => $arDados) {
            $listaTreeView->Body->addCampo( $arDados['nome'] );
        }

        $listaTreeView->montaHTML();
        $stHtmlLista = $listaTreeView->getHtml();

        if ($boJS) {
            $stHtmlLista = self::formatarJavaScript($stHtmlLista);
        }

        return $stHtmlLista;
    }

    /**
     * Gerador de item em array de hiddens
     * @param  string $nome  nome do elemento do array
     * @param  mixed  $valor o valor do elemento do array
     * @return string código HTML do hidden
     */
    public static function gerarHidden($nome, $valor)
    {
        $obHdn = new Hidden();
        $obHdn->setName("{$nome}[]");
        $obHdn->setValue($valor);

        return $obHdn;
    }

}
