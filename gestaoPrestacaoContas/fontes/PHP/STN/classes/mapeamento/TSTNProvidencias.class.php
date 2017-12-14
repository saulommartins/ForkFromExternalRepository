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
    * Classe de Mapeamento da tabela stn.providencias
    * Data de Criação   : 01/06/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
 */

include_once CLA_PERSISTENTE;

class TSTNProvidencias extends Persistente
{
    /**
     * Método Construtor da classe TSTNRiscosFiscais
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('stn.providencias');
        $this->setCampoCod        ('cod_providencia');
        $this->setComplementoChave('cod_risco, cod_entidade, exercicio');

        $this->AddCampo('cod_providencia', 'integer', true, ''    , true , false);
        $this->AddCampo('cod_risco'      , 'integer', true, ''    , true , true);
        $this->AddCampo('cod_entidade'   , 'integer', true, ''    , true , true);
        $this->AddCampo('exercicio'      , 'char'   , true, '4'   , true , true);
        $this->AddCampo('descricao'      , 'varchar', true, '450' , false, false);
        $this->AddCampo('valor'          , 'numeric', true, '14,2', false, false);
    }

    /**
     * Método que retorna as providências dos riscos fiscais
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz <eduardo.schitz@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listProvidencias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT providencias.cod_providencia
                 , providencias.cod_risco
                 , providencias.cod_entidade
                 , providencias.exercicio
                 , providencias.descricao
                 , providencias.valor
              FROM stn.providencias
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
