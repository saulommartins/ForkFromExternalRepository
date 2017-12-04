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
    * Mapeamento da tabela stn.aporte_recurso_rpps
    * Data de Criação   : 05/04/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Configuração

    * Casos de uso: uc-02.08.07
*/

include_once CLA_PERSISTENTE;

class TSTNAporteRecursoRPPS extends Persistente
{
    /**
     * Método Construtor da classe TSTNAporteRecursoRPPS
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('stn.aporte_recurso_rpps');
        $this->setCampoCod        ('cod_aporte');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('cod_aporte'   , 'integer', true, ''    , true , false);
        $this->AddCampo('descricao'    , 'varchar', true, ''    , false, false);
        $this->AddCampo('exercicio'    , 'varchar', true, '4'   , true , false);
        $this->AddCampo('grupo'        , 'integer', true, ''    , true , true );
        $this->AddCampo('valor'        , 'numeric', true, '14,2', false, false);
    }

    /**
     * Método que retorna os grupos de aportes
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarAporteRecursoRPPSGrupo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT cod_grupo
                 , exercicio
                 , descricao
              FROM stn.aporte_recurso_rpps_grupo
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Método que retorna os aportes de recursos
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarAporteRecursoRPPS(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT cod_aporte
                 , cod_grupo
                 , exercicio
                 , descricao
              FROM stn.aporte_recurso_rpps
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
}
