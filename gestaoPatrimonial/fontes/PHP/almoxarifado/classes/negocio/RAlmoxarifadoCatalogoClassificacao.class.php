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
    * Classe de Regra de Classificação de Catálogo
    * Data de Criação   : 18/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @package URBEM
    * @subpackage Regra

    $Revision: 20836 $
    $Name$
    $Autor: $
    $Date: 2007-03-09 10:27:39 -0300 (Sex, 09 Mar 2007) $

    * Casos de uso: uc-03.03.05
*/

/*
$Log$
Revision 1.51  2007/03/09 13:27:39  hboaventura
Alteração na consulta para ordenar os itens

Revision 1.50  2007/01/15 22:00:34  diego
Bug #8105#

Revision 1.49  2006/12/08 18:25:36  tonismar
*** empty log message ***

Revision 1.48  2006/12/08 17:54:51  tonismar
bug #7773

Revision 1.47  2006/12/08 16:16:46  tonismar
bug #7773

Revision 1.45  2006/12/07 15:41:08  tonismar
bug #7773

Revision 1.44  2006/12/07 12:44:31  tonismar
bug #7764

Revision 1.43  2006/12/06 18:09:11  tonismar
bug #7657

Revision 1.42  2006/11/29 17:18:21  tonismar
bug #7657

Revision 1.41  2006/11/29 17:02:53  tonismar
bug #7657

Revision 1.40  2006/10/06 17:34:55  leandro.zis
Bug #5798#

Revision 1.39  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.38  2006/07/06 12:09:31  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                                     );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                             );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogo.class.php"                                 );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php"                             );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoClassificacaoNivel.class.php"                    );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoClassificacao.class.php"                 );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoClassificacao.class.php"         );

/**
    * Classe de Regra de Classificação de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott
*/
class RAlmoxarifadoCatalogoClassificacao
{

    /**
        * @access Private
        * @var Object
    */
    public $obTAlmoxarifadoClassificacaoNivel;

    /**
        * @access Private
        * @var Object
    */
    public $obTAlmoxarifadoCatalogoClassificacao;

    /**
        * @access Private
        * @var Object
    */

    public $obRAlmoxarifadoCatalogo;

    /**
        * @access Private
        * @var Object
    */
    public $obRCadastroDinamico;

    /**
        * @access Private
        * @var Integer
    */

    public $inCodigo;

    /**
        * @access Private
        * @var Integer
    */

    public $inCodigoNivel;

    /**
        * @access Private
        * @var Integer
    */

     var $inNivel;

     /**
         * @access Private
         * @var String
      */

    public $stDescricao;

    /**
        * @access Private
        * @var String
    */

    public $stEstrutural;

    /**
        * @access Private
        * @var String
    */

    public $stEstruturalMae;

    /**
        * @access Private
        * @var String
    */

    public $stPrimeiroNivel;

    /**
        * @access Private
        * @var String
    */

    public $stUltimoNivel;

    /**
        * @access Private
        * @var boolean
    */
    public $obRComboCompleta;

     /**
         * @access Public
         * @return Integer
     */

    public function setNivel($inNivel) { $this->inNivel = $inNivel; }

    public function setCodigo($inCodigo) { $this->inCodigo = $inCodigo; }

    /**
        * @access Public
        * @return Integer
    */

    public function setCodigoNivel($inCodigoNivel) { $this->inCodigoNivel = $inCodigoNivel; }

    /**
        * @access Public
        * @return Integer
    */

    public function setPrimeiroNivel($stPrimeiroNivel) { $this->stPrimeiroNivel = $stPrimeiroNivel; }

    /**
        * @access Public
        * @return Integer
    */

    public function setUltimoNivel($stUltimoNivel) { $this->stUltimoNivel = $stUltimoNivel; }

    /**
        * @access Public
        * @return Integer
    */

    public function setDescricao($stDescricao) { $this->stDescricao = $stDescricao; }

    /**
        * @access Public
        * @return String
    */

    public function setEstrutural($stEstrutural) { $this->stEstrutural = $stEstrutural; }

    /**
        * @access Public
        * @return String
    */

    public function setEstruturalMae($stValor) { $this->stEstruturalMae = $stValor; }

    /**
        * @access Public
        * @param Object $Valor
    */
    public function setRCadastroDinamico($valor) { $this->obRCadastroDinamico   = $valor; }

    /**
        * @access Public
        * @param boolean
    */
    public function setRComboClassificacaoCompleta($boRComboCompleta) { $this->obRComboCompleta = $boRComboCompleta;}

    /**
        * @access Public
        * @return array int
    */
    public function setCodNivel($inCodNivel) { $this->arCodNivel[] = $inCodNivel;}

    /**
         * @access Public
         * @return String
     */

     function getNivel() { return $this->inNivel; }

    /**
        * @access Public
        * @return String
    */

    public function getCodigo() { return $this->inCodigo; }

    /**
        * @access Public
        * @return String
    */

    public function getCodigoNivel() { return $this->inCodigoNivel; }

    /**
        * @access Public
        * @return String
    */

    public function getPrimeiroNivel() { return $this->stPrimeiroNivel; }

    /**
        * @access Public
        * @return String
    */

    public function getUltimoNivel() { return $this->stUltimoNivel; }

    /**
        * @access Public
        * @return String
    */

    public function getDescricao() { return $this->stDescricao; }

    /**
        * @access Public
        * @return String
    */

    public function getEstrutural() { return $this->stEstrutural; }

    /**
        * @access Public
        * @return String
    */

    public function getEstruturalMae() { return $this->stEstruturalMae; }

    /**
        * @access Public
        * @return String
    */

    public function getRCadastroDinamico() {return $this->RCadastroDinamico; }

    /**
        * @access Public
        * @return array int
    */
    public function getCodNivel() { return $this->arCodNivel;}

    /**
        * @access Public
        * @return boolean
    */
    public function getRComboClassificacaoCompleta() { return $this->obRComboCompleta;}

    /**
         * Método construtor
         * @access Public
         * @param Object Reference $obRAlmoxarifadoCatalogo
    */

    public function RAlmoxarifadoCatalogoClassificacao()
    {
        $this->obRAlmoxarifadoCatalogo = new RAlmoxarifadoCatalogo();
        $this->obTransacao  = new Transacao ;

        $this->obTAlmoxarifadoCatalogoClassificacao = new TAlmoxarifadoCatalogoClassificacao;
        $this->obTAlmoxarifadoClassificacaoNivel    = new TAlmoxarifadoClassificacaoNivel;

        // Atributos
        $this->setRCadastroDinamico  ( new RCadastroDinamico            );
        $this->obRCadastroDinamico->setPersistenteAtributos ( new TAlmoxarifadoAtributoCatalogoClassificacao );
        $this->obRCadastroDinamico->setCodCadastro( 1 );
        $this->obRCadastroDinamico->obRModulo->setCodModulo( 29 );
    }

    public function incluirClassificacao($boTransacao = '')
    {

        $boFlagTransacao = false;

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->proximoCod( $this->inCodigo, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_classificacao', $this->getCodigo());
                $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_catalogo', $this->obRAlmoxarifadoCatalogo->getCodigo());
                $this->obTAlmoxarifadoCatalogoClassificacao->setDado("cod_estrutural", $this->getEstrutural());
                $this->obTAlmoxarifadoCatalogoClassificacao->setDado('descricao', $this->getDescricao());
                $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->inclusao( $boTransacao );

                if (!$obErro->ocorreu()) {
                    $this->obRCadastroDinamico->setChavePersistenteValores( array( "cod_catalogo" => $this->obRAlmoxarifadoCatalogo->getCodigo(), "cod_classificacao" => $this->getCodigo() ) );
                    $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                }
            }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
        }

        return $obErro;
    }

    public function incluir($boTransacao = '')
    {
        $boFlagTransacao = false;
        $rsContaNiveis = new RecordSet();

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if (!$obErro->ocorreu() ) {
            $this->listarPorNome($rsClassificacoes, $boTransacao);
            if ( $rsClassificacoes->getNumLinhas() > 0)
                $obErro->setDescricao("Já existe um nivel com essa descrição para este catálogo");
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->proximoCod( $this->inCodigo, $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_classificacao', $this->getCodigo());
                $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_catalogo', $this->obRAlmoxarifadoCatalogo->getCodigo());
                $this->obTAlmoxarifadoCatalogoClassificacao->setDado("cod_estrutural", $this->getEstrutural());
                $this->obTAlmoxarifadoCatalogoClassificacao->setDado('descricao', $this->getDescricao());

                $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->inclusao( $boTransacao );

                if (!$obErro->ocorreu()) {
                    $obErro = $this->obRAlmoxarifadoCatalogo->listarNiveis($rsContaNiveis, $boTransacao);

                    if (!$obErro->ocorreu()) {
                        $arCodNivel = $this->getCodNivel();
                        for ($inCount = 0; $inCount < $rsContaNiveis->getNumLinhas(); $inCount++) {
                            $this->obTAlmoxarifadoCatalogoClassificacao->setDado("codEstruturalMae", $this->getEstruturalMae());

                            $rsDadosNivel = new RecordSet();

                            if ($rsContaNiveis->arElementos[$inCount]['nivel'] <  $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel()) {
                                $inCodNivel = $arCodNivel[$inCount];
                            } elseif ($rsContaNiveis->arElementos[$inCount]['nivel'] == $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel()) {
                                $rsMascara = new RecordSet();
                                $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->retornaMascaraCompleta($rsMascara, $boTransacao);
                                $masc = strpos($rsMascara->getCampo("mascara"), '.') ? $rsMascara->getCampo("mascara") : '0';
                                $this->obTAlmoxarifadoCatalogoClassificacao->setDado("mascaraCompleta" , $masc);

                                $rsEstrutural = new RecordSet();
                                $obErro = $this->getProximoEstrutural($rsEstrutural, $boTransacao);
                                $inCodNivel = $rsEstrutural->getCampo("livre");

                            } else {
                                $inCodNivel = '0';
                            }

                            $this->obTAlmoxarifadoClassificacaoNivel->setDado('cod_catalogo', $this->obRAlmoxarifadoCatalogo->getCodigo());
                            $this->obTAlmoxarifadoClassificacaoNivel->setDado('nivel', $rsContaNiveis->arElementos[$inCount]['nivel']);
                            $this->obTAlmoxarifadoClassificacaoNivel->setDado('cod_classificacao', $this->getCodigo());
                            $this->obTAlmoxarifadoClassificacaoNivel->setDado('cod_nivel', $inCodNivel);

                            $obErro = $this->obTAlmoxarifadoClassificacaoNivel->inclusao( $boTransacao );

                        }

                        if (!$obErro->ocorreu()) {
                            $this->obRAlmoxarifadoCatalogo->obTAlmoxarifadoCatalogo->setDado('codCatalogo', $this->obRAlmoxarifadoCatalogo->getCodigo());
                            $this->obRAlmoxarifadoCatalogo->obTAlmoxarifadoCatalogo->setDado('codClassificacao', $this->getCodigo());
                            $this->obRAlmoxarifadoCatalogo->obTAlmoxarifadoCatalogo->setDado('cod_estrutural', $this->getEstrutural());

                            $obErro = $this->obRAlmoxarifadoCatalogo->obTAlmoxarifadoCatalogo->atualizaCodigoEstrutural($boTransacao);

                            if (!$obErro->ocorreu()) {
                                $this->obRCadastroDinamico->setChavePersistenteValores( array( "cod_catalogo" => $this->obRAlmoxarifadoCatalogo->getCodigo(), "cod_classificacao" => $this->getCodigo() ) );
                                $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                            }
                        }
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            $obErro = $this->consultar( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogoClassificacao );

        return $obErro;
    }

    public function alterar($boTransacao = '')
    {
        $boFlagTransacao = false;
        $boFlagErro = false;

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if (!$obErro->ocorreu() ) {
            $this->listarPorNome($rsClassificacoes, $boTransacao);
            while (!$rsClassificacoes->eof()) {
               if ( $rsClassificacoes->getCampo('cod_classificacao') != $this->getCodigo())
                  $boFlagErro = true;
               else
                  $boFlagErro = false;
               $rsClassificacoes->proximo();
            }
            if ($boFlagErro)
               $obErro->setDescricao("Já existe um nivel com essa descrição para este catálogo");
        }

        $rsRecordSetCodEstrutural = new RecordSet;
        $rsRecordSetCodEstrutural = $this->validaCodEstruturalNaoExiste();

        if ( $rsRecordSetCodEstrutural->getNumLinhas() > 0) {
            if ($this->getCodigo() != $rsRecordSetCodEstrutural->getCampo('cod_classificacao')) {
                $obErro->setDescricao("Já existe uma classificação cadastrada com o mesmo Código Estrutural ( Numero:".$this->getEstruturalMae().") para este catálogo.");
            }
        }

        if ( !$obErro->ocorreu() ) {
            $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_classificacao', $this->getCodigo());
            $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_catalogo', $this->obRAlmoxarifadoCatalogo->getCodigo());
            $this->obTAlmoxarifadoCatalogoClassificacao->setDado('descricao', $this->getDescricao());
            $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_estrutural', $this->getEstruturalMae());

            $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->alteracao( $boTransacao );

            if (!$obErro->ocorreu()) {
                $this->obRAlmoxarifadoCatalogo->obTAlmoxarifadoCatalogo->setDado('codCatalogo', $this->obRAlmoxarifadoCatalogo->getCodigo());
                $this->obRAlmoxarifadoCatalogo->obTAlmoxarifadoCatalogo->setDado('codClassificacao', $this->getCodigo());

                $this->obRCadastroDinamico->setChavePersistenteValores( array( "cod_catalogo" => $this->obRAlmoxarifadoCatalogo->getCodigo(), "cod_classificacao" => $this->getCodigo()));

                $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
            }
        }
        if (!$obErro->ocorreu()) {
            $obErro = $this->consultar( $boTransacao );
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogoClassificacao );

        return $obErro;
    }

    public function excluir($boTransacao = '')
    {
        $this->consultar();

        $boFlagTransacao = false;
        $rsClassificacoesFilhas = new RecordSet();

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->recuperaClassificacoesFilhas($rsClassificacoesFilhas, $this->getEstrutural(), $boTransacao);

            if ($rsClassificacoesFilhas->getNumLinhas() != -1) {
                $obErro->setDescricao ("A Classificação não pode ser excluída, pois existem outras relacionadas a ela.");
            } else {
                $obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem();
                $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->setCodigo($this->getCodigo());
                $obRAlmoxarifadoCatalogoItem->listar($rsItens);
                if ($rsItens->getNumLinhas() > 0) {
                     $obErro->setDescricao ("A Classificação não pode ser excluída, pois existem itens relacionados a ela.");
                } else {
                     $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_classificacao', $this->getCodigo());
                     $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_catalogo', $this->obRAlmoxarifadoCatalogo->getCodigo());
                     $rsAtributosSelecionados = new RecordSet();

                     $this->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados );

                     $this->obRCadastroDinamico->setChavePersistenteValores( array
                                                                             (
                                                                                  "cod_catalogo" => $this->obRAlmoxarifadoCatalogo->getCodigo(),
                                                                                  "cod_classificacao" => $this->getCodigo()
                                                                             )
                                                                           );

                     $stComplementoChave = $this->obTAlmoxarifadoClassificacaoNivel->getComplementoChave();
                     $stChave = $this->obTAlmoxarifadoClassificacaoNivel->getCampoCod();

                     $this->obTAlmoxarifadoClassificacaoNivel->setCampoCod('cod_catalogo');
                     $this->obTAlmoxarifadoClassificacaoNivel->setComplementoChave( 'cod_classificacao' );

                     $this->obTAlmoxarifadoClassificacaoNivel->setDado( "cod_catalogo", $this->obRAlmoxarifadoCatalogo->getCodigo());
                     $this->obTAlmoxarifadoClassificacaoNivel->setDado( "cod_classificacao", $this->getCodigo());

                     $obErro = $this->obTAlmoxarifadoClassificacaoNivel->exclusao( $boTransacao );

                     if (!$obErro->ocorreu()) {
                         $this->obTAlmoxarifadoClassificacaoNivel->setCampoCod($stChave);
                         $this->obTAlmoxarifadoClassificacaoNivel->setComplementoChave( $stComplementoChave );

                         $obErro = $this->obRCadastroDinamico->excluir($boTransacao);
                         if (!$obErro->ocorreu()) {
                             $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->exclusao( $boTransacao );
                         }
                     }
                }
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogoClassificacao );

        return $obErro;
    }

    public function getDadosNivel(&$rsDadosNivel, $boTransacao = '', $inNivelDesejado)
    {
        $stFiltro = ' cod_classificacao = ' . ($this->getCodigo() - 1) . ' AND nivel = ' . $inNivelDesejado;

        $stFiltro = ' WHERE ' . $stFiltro;

        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaDadosNivel($rsDadosNivel, $stFiltro, '', $boTransacao);

        return $obErro;
    }

    /**
        * Alterar Catalogo
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function getProximoEstrutural(&$rsREstrutural, $boTransacao = '')
    {
        $this->obTAlmoxarifadoCatalogoClassificacao->setDado("nivel", $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel());
        $this->obTAlmoxarifadoCatalogoClassificacao->setDado("cod_catalogo", $this->obRAlmoxarifadoCatalogo->getCodigo());
        $this->obTAlmoxarifadoCatalogoClassificacao->setDado("mascara", '1');
        $this->obTAlmoxarifadoCatalogoClassificacao->setDado("cod_estrutural_mae", $this->getEstruturalMae());

        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaProximoEstrutural($rsREstrutural, $boTransacao);

        return $obErro;
    }

    public function validarEstrutural($rsEstrutural, $boTransacao='')
    {
        $stFiltro  = " cod_catalogo =  "  . $this->obRAlmoxarifadoCatalogo->getCodigo() . " AND nivel= " . $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel();

        $stFiltro = "  WHERE " . $stFiltro;

        $obErro = $this->obTAlmoxarifadoClassificacaoNivel->recuperaTodos( $rsEstrutural, $stFiltro, '', $boTransacao );

        return $obErro;

    }

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
    {
        if ($this->obRAlmoxarifadoCatalogo->getCodigo()) {
            $stFiltro  = " AND cal.cod_catalogo =  "  . $this->obRAlmoxarifadoCatalogo->getCodigo();
        }

        if (is_object($this->obRAlmoxarifadoCatalogo->roCatalogoNivel)) {
            if (is_object($this->obRAlmoxarifadoCatalogo->roCatalogoNivel)) {
                $stFiltro .= " AND cln.nivel= " . $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel();
            }
        }

        $stOrder = ($stOrder) ? $stOrder : "cal.cod_estrutural";
        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao);
        $rsRecordSet->addFormatacao('descricao', 'STRIPSLASHES');

        return $obErro;
    }

    public function listarPorNome(&$rsRecordSet , $boTransacao = "")
    {
        $inCount = 0;
        $arTmp = explode('.',$this->getEstruturalMae());

        $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->listar( $rsNiveis, "", $boTransacao );

        $rsNiveis->setPrimeiroElemento();
        while ( !$rsNiveis->eof() ) {
            if ( $rsNiveis->getCampo('nivel') == $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel() ) {
                $arEstruturado[ $rsNiveis->getCampo('nivel') - 1 ] = '%';
            } else {
                $arEstruturado[ $rsNiveis->getCampo('nivel') - 1 ] = str_pad( $arTmp[ $rsNiveis->getCampo('nivel') - 1 ], strlen( $rsNiveis->getCampo('mascara') ), '0', STR_PAD_LEFT );
            }
            $rsNiveis->proximo();
        }

        if ( is_array( $arEstruturado ) ) {

            $stEstruturado = implode('.',$arEstruturado);
        }

        if ($this->obRAlmoxarifadoCatalogo->getCodigo()) {
            $stFiltro  = " AND cal.cod_catalogo =  "  . $this->obRAlmoxarifadoCatalogo->getCodigo();
        }

        if ($this->getDescricao()) {
            $stFiltro .= " AND cal.descricao ilike '"  . $this->getDescricao()."' ";
        }

        if ( $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel() ) {
            $stFiltro .= " AND cn.nivel = ". $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel();
        }

        $stFiltro .= " AND cod_nivel <> 0 ";

        $stFiltro .= " AND cal.cod_estrutural like '". $stEstruturado."' ";

        $stOrder = '';
        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

        return $obErro;

    }

    public function listarDetalhesClassificacao(&$rsRecordSet, $stOrder = "", $boTransacao = "")
    {
        $this->stMascaraCombinada = '';
        $rsMascara = new RecordSet();

        $obErro = $this->listarMascara($rsMascara, $boTransacao);

        if (!$obErro->ocorreu()) {
            while (!$rsMascara->EOF()) {
                $this->stMascaraCombinada .= $rsMascara->getCampo('mascara').'.';
                $rsMascara->proximo();
            }

            $this->stMascaraCombinada = preg_replace('/\.$/', '', $this->stMascaraCombinada);
        }

        $stFiltro  = "cc.cod_catalogo = " . $this->obRAlmoxarifadoCatalogo->getCodigo();
        $stFiltro .= " AND cn.nivel = " . $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel();
        $stFiltro .= " AND cc.cod_classificacao = cn.cod_classificacao";
        $stFiltro .= " AND cc.cod_catalogo = cn.cod_catalogo";
        $stFiltro .= " AND publico.fn_nivel( cc.cod_estrutural ) = cn.nivel";

        if ($this->getRComboClassificacaoCompleta() == false) {
            if ($this->getEstrutural()) {
                $stFiltro .= " AND cc.cod_estrutural like ";
                $stFiltro .= " publico.fn_mascara_dinamica('" . $this->stMascaraCombinada  ."','" . $this->getEstrutural() . "')||'%'";
            }
        }

        $stFiltro = "WHERE ".$stFiltro;

        $stOrder = " ORDER BY cc.descricao";

        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaDetalhesClassificacao($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

        return $obErro;
    }

    public function listarClassificacoes(&$rsRecordSet, $stOrder = "", $boTransacao = "")
    {
        $this->stMascaraCombinada = '';
        $rsMascara = new RecordSet();

        $obErro = $this->listarMascara($rsMascara, $boTransacao);

        if (!$obErro->ocorreu()) {
            while (!$rsMascara->EOF()) {
                $this->stMascaraCombinada .= $rsMascara->getCampo('mascara').'.';
                $rsMascara->proximo();
            }

            $this->stMascaraCombinada = preg_replace('/\.$/', '', $this->stMascaraCombinada);
        }

        $stFiltro  = "cod_catalogo = " . $this->obRAlmoxarifadoCatalogo->getCodigo();

        if ($this->getEstrutural()) {
            $stFiltro .= " AND cod_estrutural like '".$this->getEstrutural()."%' ";
        }

        $stFiltro = " WHERE ".$stFiltro;

        $stOrder = " ORDER BY cod_estrutural";

        //recuperaDetalhesClassificacao
        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

        $rsRecordSet->addFormatacao('descricao', 'STRIPSLASHES');

        return $obErro;
    }

    public function listarMascara(&$rsRecordSet, $boTransacao)
    {
        $stFiltro = "WHERE cod_catalogo = " . $this->obRAlmoxarifadoCatalogo->getCodigo();
        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaMascara($rsRecordSet, $stFiltro, ' ORDER BY nivel ', $boTransacao);

        return $obErro;
    }

    /**
        * Executa um recuperaPorChave na classe Persistente
        * @access Public
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function consultar($boTransacao = "")
    {
        $this->obTAlmoxarifadoCatalogoClassificacao->setDado( "cod_catalogo", $this->obRAlmoxarifadoCatalogo->getCodigo() );
        if ( !$this->getCodigo() and $this->getEstrutural() ) {
            $stFiltro  = ' WHERE cod_catalogo   = '.$this->obRAlmoxarifadoCatalogo->getCodigo();
            $stFiltro .= ' AND   cod_estrutural = '.$this->getEstrutural();
            $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );

            $this->setCodigo( $rsRecordSet->getCampo("cod_classificacao") );
        } else {
            $this->obTAlmoxarifadoCatalogoClassificacao->setDado( "cod_classificacao"       , $this->getCodigo());
            $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaPorChave( $rsRecordSet, $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $rsRecordSet->addFormatacao('descricao', 'STRIPSLASHES');
            $this->setEstrutural($rsRecordSet->getCampo  ( "cod_estrutural" ));
            $this->setDescricao ($rsRecordSet->getCampo  ( "descricao" ));
            $obErro = $this->obRAlmoxarifadoCatalogo->consultar( $boTransacao );
        }

        return $obErro;
    }

    /**
        * Valida a alteração de máscara de um nível
        * @access Public
        * @param recordSet $rsValidaNivel
        * @param boolean $boTransacao
    */

    public function validarNivelClassificacao(&$rsValidaNivel, $boTransacao)
    {
        $stFiltro  = " cod_catalogo =  "  . $this->obRAlmoxarifadoCatalogo->getCodigo() . " AND nivel= " . $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getNivel() . " AND cod_nivel > " . $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->getMascara();

        $stFiltro = "  WHERE " . $stFiltro;

        $obErro = $this->obTAlmoxarifadoClassificacaoNivel->recuperaTodos( $rsValidaNivel, $stFiltro, '', $boTransacao );

        return $obErro;
    }

    /**
        * Busca os dados de todas as Classificações acima da informada
        * @access Public
        * @param recordset $rsClassificacoesMae
        * @param string $stEstruturalFilho
        * @param boolean $boTransacao
    */

    public function listarClassificacao(&$rsClassificacao, $boTransacao='')
    {
        $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_estrutural',$this->getEstrutural());
        $this->obTAlmoxarifadoCatalogoClassificacao->setDado('cod_catalogo'  ,$this->obRAlmoxarifadoCatalogo->getCodigo());
        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaClassificacao($rsClassificacao, '', '', $boTransacao);

        return $obErro;
    }

    /**
        * Busca os dados da Classificação Mãe de um nível qualquer
        * @access Public
        * @param recordSet $rsClassificacaoMae
        * @param string $stEstruturalFilho
    */
    public function listarClassificacaoMae(&$rsClassificacoesMae, $boTransacao='')
    {
        $obErro              = $this->listarClassificacao($rsClassificacoes, $boTransacao);
        $arRecordSet         = array();
        $rsClassificacoesMae = new RecordSet();

        $rsClassificacoes->ordena('nivel','DESC');
        $rsClassificacoes->proximo();
        while ( !$rsClassificacoes->eof() ) {
            foreach ( $rsClassificacoes->arElementos[$rsClassificacoes->getCorrente()-1] as $stKey=>$stValue ) {
                $arElementos[$stKey] = $stValue;
            }
            $arRecordSet[] = $arElementos;
            $rsClassificacoes->proximo();
        }
        $rsClassificacoesMae->preenche( $arRecordSet );
        $rsClassificacoesMae->ordena('nivel','ASC');

        return $obErro;
    }

    public function recuperaClassificacoesFilhas(&$rsClassificacoesFilhas, $stEstruturalMae, $boTransacao='')
    {

        $stFiltro  = " WHERE cod_estrutural like publico.fn_mascarareduzida('".$stEstruturalMae."')||'%' and cod_estrutural != '".$stEstruturalMae."' ";
        $stFiltro .= " AND cod_catalogo = ".$this->obRAlmoxarifadoCatalogo->getCodigo();

        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaTodos($rsClassificacoesFilhas, $stFiltro, 'cod_estrutural', $boTransacao);

        return $obErro;
    }

    public function incluirClassificacaoNivel($boTransacao = '')
    {

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $boFlagTransacao = false;
            $this->obTAlmoxarifadoClassificacaoNivel->setDado('cod_catalogo', $this->obRAlmoxarifadoCatalogo->getCodigo());
            $this->obTAlmoxarifadoClassificacaoNivel->setDado('nivel',$this->getNivel());
            $this->obTAlmoxarifadoClassificacaoNivel->setDado('cod_classificacao', $this->getCodigo());
            $this->obTAlmoxarifadoClassificacaoNivel->setDado("cod_nivel", $this->getCodigoNivel());

            $obErro = $this->obTAlmoxarifadoClassificacaoNivel->inclusao( $boTransacao );
        }

        return $obErro;
    }

    public function recuperaCodigoClassificacao(&$rsCodigoClassificacao,$boTransacao = '')
    {

        $stFiltro = " trim(cod_estrutural) = '".$this->stEstrutural."'";
        $stFiltro .= " AND cod_catalogo = ".$this->obRAlmoxarifadoCatalogo->getCodigo();
        $stFiltro = "WHERE " . $stFiltro;
        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaCodigoClassificacaoImportacao($rsCodigoClassificacao,$stFiltro,'',$boTransacao);

        return $obErro;

    }

    public function validaCodEstruturalNaoExiste()
    {
        $stFiltro  = ' WHERE cod_catalogo   = '.$this->obRAlmoxarifadoCatalogo->getCodigo();
        $stFiltro .= " AND   cod_estrutural = '".trim($this->getEstruturalMae())."'";
        $obErro = $this->obTAlmoxarifadoCatalogoClassificacao->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );

        return $rsRecordSet;
    }

}
