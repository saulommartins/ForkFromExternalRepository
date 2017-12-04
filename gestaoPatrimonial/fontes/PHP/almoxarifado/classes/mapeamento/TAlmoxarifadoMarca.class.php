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
    * Classe de mapeamento da tabela ALMOXARIFADO.MARCA
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.03.03

    $Id: TAlmoxarifadoMarca.class.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.MARCA
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
  */
class TAlmoxarifadoMarca extends Persistente
{
    /**
      * Método Construtor
      * @access Private
      */
    public function TAlmoxarifadoMarca()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.marca');

        $this->setCampoCod('cod_marca');
        $this->setComplementoChave('');

        $this->AddCampo('cod_marca','integer',true,'',true,false);
        $this->AddCampo('descricao','varchar',true,'80',false,false);
    }

    public function recuperaMarca(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMarca",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMarca()
    {
        $stSql = "
            SELECT  marca.cod_marca
                 ,  marca.descricao
              FROM  almoxarifado.marca ";

        if ($this->getDado("cod_marca"))
            $stFiltro  = " AND marca.cod_marca = ".$this->getDado("cod_marca");

        if ($this->getDado("descricao"))
            $stFiltro .= " AND upper(marca.descricao) like upper('".$this->getDado("descricao")."')";

        $stFiltro = ($stFiltro != "") ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";

        return $stSql.$stFiltro;
    }
}

?>
