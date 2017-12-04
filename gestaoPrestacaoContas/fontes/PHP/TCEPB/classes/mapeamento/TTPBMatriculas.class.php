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
    * Página de mapeamento para geração do arquivo Matriculas.txt
    * Data de Criação   : 01/03/2011

    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Eduardo Paculski Schitz <eduardo.schitz@cnm.org.br>

    * @package URBEM
    * @subpackage TCEPB
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTPBMatriculas extends Persistente
{

    /**
     * Método construtor
     *
     * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Eduardo Paculski Schitz <eduardo.schitz@cnm.org.br>
     *
     * @return void
    */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
     * Método que monta o sql necessário para recuperar os dados necessários para gerar o arquivo matriculas.txt
     *
     * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Eduardo Paculski Schitz <eduardo.schitz@cnm.org.br>
     *
     * @return String
    */
    public function montaRecuperaRelacionamento()
    {
        $stSql  = "SELECT *
                     FROM tcepb.recupera_matriculas('".$this->getDado('stSchemaEntidade')."', '".$this->getDado('inMes').$this->getDado('stExercicio')."') AS (
                         cpf                      VARCHAR
                       , numcgm                   INTEGER
                       , cod_cargo                INTEGER
                       , matricula                INTEGER
                       , dt_admissao              VARCHAR
                     )";
        return $stSql;
    }
}
