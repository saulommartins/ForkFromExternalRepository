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
    * Classe de Regra de Negócio Notas Explicativas
    * Data de Criação   : 03/09/2007

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo S. Rodrigues

    * @ignore

    * $Id: RContabilidadeNotasExplicativas.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"      );

class RContabilidadeNotasExplicativas
{

    /**
        * @var Objeto
        * @access Private
        */
    public $obTransacao;
    /**
        * @var Integer
        * @access Private
    */
    public $inCodAcao;
    /**
        * @var String
        * @access Private
    */
    public $stNotaExplicativa;

    /**
        * @access Public
        * @param Object $valor
        */
        function setTransacao($valor) { $this->obTransacao       = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setCodAcao($valor) { $this->inCodAcao         = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setNotaExplicativa($valor) { $this->stNotaExplicativa = $valor; }
    /**
         * @access Public
         * @param String $valor

    /**
        * @access Public
        * @return Object
        */
    public function getTransacao() { return $this->obTransacao;        }
    /**
         * @access Public
         * @return Integer
    */
    public function getCodAcao() { return $this->inCodAcao;          }
    /**
         * @access Public
         * @return String
    */
    public function getNotaExplicativa() { return $this->stNotaExplicativa;  }

    /**
    * Método Construtor
    * @access Private
    */
    public function RContabilidadeNotasExplicativas()
    {
        $this->setTransacao ( new Transacao );
    }

    /**
        * Salva as NOTAS EXPLICATIVAS no banco de dados
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
     function incluir($boTransacao = "")
     {
         include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeNotasExplicativas.class.php" );
         $obTContabilidadeNotaExplicativa = new TContabilidadeNotasExplicativas;
         $obTContabilidadeNotaExplicativa->recuperaNotaExplicativa($rsAnexo,$boTransacao);

         $boFlagTransacao = false;
         $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
         if ( !$obErro->ocorreu() ) {
             $stFiltro = " AND cod_acao = ".$this->getCodAcao();
             $obErro = $obTContabilidadeNotaExplicativa->recuperaNotaExplicativa($rsAnexo, $stFiltro,'',$boTransacao);

             if ( !$obErro->ocorreu() ) {
                 $obTContabilidadeNotaExplicativa->setDado( "cod_acao"         ,  $this->getCodAcao()         );
                 $obTContabilidadeNotaExplicativa->setDado( "nota_explicativa" ,  $this->getNotaExplicativa() );

                 $obErro = $obTContabilidadeNotaExplicativa->inclusao( $boTransacao );

                 $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeNotaExplicativa );
             }
         }

         return $obErro;
     }

}

?>
