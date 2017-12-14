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
 * Mapeamento da tabela tesouraria.cheque_impressora_terminal
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TTesourariaChequeImpressoraTerminal extends Persistente
{
    /**
     * Método Construtor da classe TTesourariaChequeImpressoraTerminal
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tesouraria.cheque_impressora_terminal');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_terminal, timestamp_terminal, cod_impressora');

        $this->AddCampo('cod_terminal'       ,'integer'  , true, ''  , true, true );
        $this->AddCampo('timestamp_terminal' ,'timestamp', true, ''  , true, true );
        $this->AddCampo('cod_impressora'     ,'integer'  , true, ''  , true, true );
    }

    /**
     * Método que recupera a impressora vinculada ao terminal
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
     public function findImpressoraTerminal(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
     {
         $stSql = "
            SELECT impressora.cod_impressora
                 , impressora.fila_impressao
              FROM tesouraria.cheque_impressora_terminal
        INNER JOIN administracao.impressora
                ON cheque_impressora_terminal.cod_impressora = impressora.cod_impressora
        INNER JOIN tesouraria.terminal
                ON cheque_impressora_terminal.cod_terminal       = terminal.cod_terminal
               AND cheque_impressora_terminal.timestamp_terminal = terminal.timestamp_terminal
         ";

         return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
     }

}
