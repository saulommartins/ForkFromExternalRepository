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
* Classe de regra de negócio para Pessoal-AssentamentoVinculado
* Data de Criação: 04/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"                                     );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoVinculado.class.php"          );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoVinculadoFuncao.class.php"    );

class RPessoalAssentamentoVinculado
{
    /**
        * @access Private
        * @var Integer
    */
    public $inDiasIncidencia;
    /**
        * @access Private
        * @var String
    */
    public $stCondicao;
    /**
        * @access Private
        * @var Integer
    */
    public $inDiasProtelarAverbar;
    /**
        * @access Private
        * @var Object
    */
    public $obTPessoalAssentamentoVinculado;
    /**
        * @access Private
        * @var Object
    */
    public $obTPessoalAssentamentoVinculadoFuncao;
    /**
        * @access Private
        * @var Object
    */
    public $obRFuncao;
    /**
        * @access Private
        * @var Object
    */
    public $obRPessoalAssentamento1;
    /**
        * @access Private
        * @var Object
    */
    public $obRPessoalAssentamento2;
    /**
        * @access Private
        * @var Object
    */
    public $obTransacao;

    /**
        * @access Public
        * @param Integer $Valor
    */
    public function setDiasIncidencia($valor) { $this->inDiasIncidencia             = $valor; }
    /**
        * @access Public
        * @param String $Valor
    */
    public function setCondicao($valor) { $this->stCondicao                   = $valor; }
    /**
        * @access Public
        * @param Integer $Valor
    */
    public function setDiasProtelarAverbar($valor) { $this->inDiasProtelarAverbar        = $valor; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setTPessoalAssentamentoVinculado($valor) { $this->obTPessoalAssentamentoVinculado  = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setTPessoalAssentamentoVinculadoFuncao($valor) { $this->obTPessoalAssentamentoVinculadoFuncao  = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setRFuncao($valor) { $this->obRFuncao = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setRPessoalAssentamento1($valor) { $this->obRPessoalAssentamento1 = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setRPessoalAssentamento2($valor) { $this->obRPessoalAssentamento2 = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setTransacao($valor) { $this->obTransacao                  = $valor; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setRORPessoalCondicaoAssentamento($valor) { $this->roRPessoalCondicaoAssentamento = $valor; }

    /**
        * @access Public
        * @return Integer
    */
    public function getDiasIncidencia() { return $this->inDiasIncidencia              ; }
    /**
        * @access Public
        * @return String
    */
    public function getCondicao() { return $this->stCondicao                    ; }
    /**
        * @access Public
        * @return Integer
    */
    public function getDiasProtelarAverbar() { return $this->inDiasProtelarAverbar         ; }
    /**
        * @access Public
        * @return Object
    */
    public function getTPessoalAssentamentoVinculado() { return $this->obTPessoalAssentamentoVinculado   ; }
    /**
        * @access Public
        * @return Object
    */
    public function getTPessoalAssentamentoVinculadoFuncao() { return $this->obTPessoalAssentamentoVinculadoFuncao   ; }
    /**
        * @access Public
        * @return Object
    */
    public function getRFuncao() { return $this->obRFuncao                         ; }
    /**
        * @access Public
        * @return Object
    */
    public function getRPessoalAssentamento1() { return $this->obRPessoalAssentamento1            ; }
    /**
        * @access Public
        * @return Object
    */
    public function getRPessoalAssentamento2() { return $this->obRPessoalAssentamento2            ; }
    /**
        * @access Public
        * @return Object
    */
    public function getTransacao() { return $this->obTransacao               ; }
    /**
        * @access Public
        * @return Object
    */
    public function getRORPessoalCondicaoAssentamento() { return $this->roRPessoalCondicaoAssentamento ; }

    /**
        * Método construtor
        * @access Private
    */
    public function RPessoalAssentamentoVinculado(&$obRPessoalAssentamento1,&$obRPessoalAssentamento2,&$roRPessoalCondicaoAssentamento)
    {
        $this->setTPessoalAssentamentoVinculado             ( new TPessoalAssentamentoVinculado         );
        $this->setTPessoalAssentamentoVinculadoFuncao       ( new TPessoalAssentamentoVinculadoFuncao   );
        $this->setRFuncao                                   ( new RFuncao                               );
        $this->setRPessoalAssentamento1                     ( $obRPessoalAssentamento1                 );
        $this->setRPessoalAssentamento2                     ( $obRPessoalAssentamento2                 );
        $this->setRORPessoalCondicaoAssentamento            ( $roRPessoalCondicaoAssentamento          );
        $this->setTransacao                                 ( new Transacao                             );
    }

    /**
        * Inclui dados de assentamento vinculado no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirAssentamentoVinculado($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalAssentamentoVinculado->setDado("cod_condicao",                         $this->roRPessoalCondicaoAssentamento->getCodCondicao()         );
            $this->obTPessoalAssentamentoVinculado->setDado("timestamp",                            $this->roRPessoalCondicaoAssentamento->getTimestamp()           );
            $this->obTPessoalAssentamentoVinculado->setDado("cod_assentamento",                     $this->obRPessoalAssentamento1->getCodAssentamento()            );
            $this->obTPessoalAssentamentoVinculado->setDado("cod_assentamento_assentamento",        $this->obRPessoalAssentamento2->getCodAssentamento()            );
            $this->obTPessoalAssentamentoVinculado->setDado("condicao",                             $this->getCondicao()                                            );
            $this->obTPessoalAssentamentoVinculado->setDado("dias_incidencia",                      $this->getDiasIncidencia()                                      );
            $this->obTPessoalAssentamentoVinculado->setDado("dias_protelar_averbar",                $this->getDiasProtelarAverbar()                                 );
            $obErro = $this->obTPessoalAssentamentoVinculado->inclusao( $boTransacao );

            if ( !$obErro->ocorreu and $this->obRFuncao->getCodFuncao() != "" ) {
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado('cod_assentamento_assentamento', $this->obRPessoalAssentamento2->getCodAssentamento()       );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado('cod_condicao',                  $this->roRPessoalCondicaoAssentamento->getCodCondicao()    );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado('timestamp',                     $this->roRPessoalCondicaoAssentamento->getTimestamp()      );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado('cod_assentamento',              $this->obRPessoalAssentamento1->getCodAssentamento()       );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado('cod_funcao',                    $this->obRFuncao->getCodFuncao()                           );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado("cod_modulo",                    $this->obRFuncao->obRBiblioteca->roRModulo->getCodModulo() );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado("cod_biblioteca",                $this->obRFuncao->obRBiblioteca->getCodigoBiblioteca()     );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado("condicao",                      $this->getCondicao()                                       );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado("dias_incidencia",               $this->getDiasIncidencia()                                 );
                $this->obTPessoalAssentamentoVinculadoFuncao->setDado("dias_protelar_averbar",         $this->getDiasProtelarAverbar()                            );
                $obErro = $this->obTPessoalAssentamentoVinculadoFuncao->inclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamento );

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
    public function listarAssentamentoVinculado(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = '';
        if( $this->roRPessoalCondicaoAssentamento->getCodCondicao() )
            $stFiltro .= " AND pca.cod_condicao = ".$this->roRPessoalCondicaoAssentamento->getCodCondicao()." ";

        if( $this->obRPessoalAssentamento1->getCodAssentamento() )
            $stFiltro .= " AND pca.cod_assentamento = ".$this->obRPessoalAssentamento1->getCodAssentamento()." ";

        if( $this->obRPessoalAssentamento2->getTimestamp() )
            $stFiltro .= " AND pca.timestamp = '".$this->obRPessoalAssentamento2->getTimestamp()."' ";

        if( $this->obRPessoalAssentamento2->getCodAssentamento() )
            $stFiltro .= " AND pca.cod_assentamento_assentamento = ".$this->obRPessoalAssentamento2->getCodAssentamento()." ";
        if ($stFiltro != '') {

            $stFiltro = 'where '. substr($stFiltro, 5);
        }
        $obErro = $this->obTPessoalAssentamentoVinculado->recuperaAssentamentoVinculado ( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

}
