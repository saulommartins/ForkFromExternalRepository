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
    * Classe de Regra de Tipo de Item
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.06 uc-03.03.07
*/

/*
$Log$
Revision 1.4  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:09:32  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoTipoItem.class.php");

/**
    * Classe de Regra de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/

class RAlmoxarifadoTipoItem
{

    /**
        * @access Private
        * @var Object
    */

    public $obTAlmoxarifadoCatalogoItem;

    /**
        * @access Private
        * @var Integer
    */

    public $inCodigo;

    /**
        * @access Private
        * @var String
    */

    public $stDescricao;

    /**
        * @access Public
        * @return Integer
    */

    public function setCodigo($inCodigo) { $this->inCodigo = $inCodigo; }

    /**
        * @access Public
        * @return Integer
    */

    public function setDescricao($stDescricao) { $this->stDescricao = $stDescricao; }

    /**
        * @access Public
        * @return Integer
    */

    public function getCodigo() { return $this->inCodigo; }

    /**
        * @access Public
        * @return String
    */

    public function getDescricao() { return $this->stDescricao; }

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoTipoItem()
    {
        $this->obTransacao  = new Transacao ;
    }

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function listar(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
    {
        $obTAlmoxarifadoTipoItem = new TAlmoxarifadoTipoItem;
        $stFiltro = "";
        $boTransacao = "";

        if ($this->stDescricao) {
            $obTAlmoxarifadoTipoItem->setDado('stDescricao', $this->stDescricao );
        }

        $obErro = $obTAlmoxarifadoTipoItem->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
        $obTAlmoxarifadoTipoItem = new TAlmoxarifadoTipoItem;
        $obTAlmoxarifadoTipoItem->setDado("cod_tipo", $this->inCodigo);
        $obErro = $obTAlmoxarifadoTipoItem->recuperaPorChave( $rsRecordSet, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->setDescricao($rsRecordSet->getCampo("descricao"));

        }

        return $obErro;
    }

}
