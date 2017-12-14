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
 * Mapeamento da tabela stn.identificador_risco_fiscal
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Desenvolvedor   Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id: TSTNIdentificadorRiscoFiscal.class.php 59612 2014-09-02 12:00:51Z gelson $
 */

include_once CLA_PERSISTENTE;

class TSTNIdentificadorRiscoFiscal extends Persistente
{
    /**
     * Método Construtor da classe TSTNIdentificadorRiscoFiscal
     *
     * @author      Desenvolvedor   Eduardo Schitz <eduardo.schitz@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('stn.identificador_risco_fiscal');
        $this->setCampoCod        ('cod_identificador');

        $this->AddCampo('cod_identificador', 'integer', true ,   '', true , false);
        $this->AddCampo('descricao'        , 'varchar', false, '40', false, false);
    }

    /**
     * Método que retorna os identificadores
     *
     * @author      Desenvolvedor   Eduardo Schitz <eduardo.schitz@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listIdentificadores(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT identificador_risco_fiscal.cod_identificador
                 , identificador_risco_fiscal.descricao
              FROM stn.identificador_risco_fiscal
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
