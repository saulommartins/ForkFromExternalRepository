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
  * Arquivo de mapeamento - Exportação Arquivos TCEMG - RESPINF.csv
  * Data de Criação: 11/03/2016

  * @author Analista:      Dagiane
  * @author Desenvolvedor: Arthur Cruz
  *
  * @ignore
  * $Id: TTCEMGRESPINF.class.php 65302 2016-05-11 11:35:18Z evandro $
  * $Date: 2016-05-11 08:35:18 -0300 (Wed, 11 May 2016) $
  * $Author: evandro $
  * $Rev: 65302 $
  *
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEMGRESPINF extends Persistente
{
    public function __construct() {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="") {
        return $this->executaRecupera("montaRecuperaDados",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDados () {
        $stDataInicial = SistemaLegado::dataToSql($this->getDado('dt_inicial'));
        $stDataInicialFinal = SistemaLegado::dataToSql($this->getDado('dt_final'));
        $stSql = "
          SELECT 
               sw_cgm_pessoa_fisica.cpf AS cpf
               , '".$stDataInicial."'AS dt_inicio
               , '".$stDataInicialFinal."' AS dt_final
            FROM tcemg.configuracao_orgao
      INNER JOIN sw_cgm
              ON sw_cgm.numcgm = configuracao_orgao.num_cgm
      INNER JOIN sw_cgm_pessoa_fisica
              ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
           WHERE configuracao_orgao.dt_inicio <= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND configuracao_orgao.dt_fim >= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
             AND configuracao_orgao.exercicio = '".$this->getDado('exercicio')."'
             AND configuracao_orgao.cod_entidade IN (".$this->getDado('entidades').")
             -- tipo responsavel pela folha somente
             AND configuracao_orgao.tipo_responsavel = 5
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>