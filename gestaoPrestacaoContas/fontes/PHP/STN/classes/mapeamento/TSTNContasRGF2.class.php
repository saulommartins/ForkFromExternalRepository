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
    * Mapeamento da tabela stn.contas_rgf_2
    * Data de Criação   : 28/05/2013

    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage Configuração

*/

include_once CLA_PERSISTENTE;

class TSTNContasRGF2 extends Persistente
{
    /**
     * Método Construtor da classe TSTNContasRGF2
     *
     * @author    Desenvolvedor   Eduardo Paculski Schitz
     *
     * @return void
     */
    public function TSTNContasRGF2()
    {
        parent::Persistente();

        $this->setTabela          ('stn.contas_rgf_2');
        $this->setCampoCod        ('cod_conta');

        $this->AddCampo('cod_conta', 'integer', true, ''    , true , false);
        $this->AddCampo('descricao', 'varchar', true, ''    , false, false);
    }

    /**
     * Método que retorna as contas que devem ser configuradas
     *
     * @author      Desenvolvedor   Eduardo Paculski Schitz
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarContasRGF2(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT cod_conta
                 , descricao
              FROM stn.contas_rgf_2
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
}
