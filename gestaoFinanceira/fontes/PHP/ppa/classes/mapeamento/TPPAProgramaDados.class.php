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
    * Classe de mapeamento da tabela PPA.PROGRAMA_DADOS
    * Data de Criação: 03/10/2008

    * @author Analista: Bruno Ferreira
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAProgramaDados extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPPAProgramaDados()
    {
        parent::Persistente();

        $this->setTabela('ppa.programa_dados');

        $this->setCampoCod('cod_programa');
        $this->setComplementoChave('timestamp_programa_dados');

        $this->AddCampo('cod_programa'              , 'sequence' , true, ''   , true , false);
        $this->AddCampo('timestamp_programa_dados'  , 'timestamp', true, ''   , false, false);
        $this->AddCampo('cod_tipo_programa'         , 'integer'  , true, ''   , true , true);
        $this->AddCampo('identificacao'             , 'varchar'  , true, '280', false, false);
        $this->AddCampo('diagnostico'               , 'varchar'  , true, '480', false, false);
        $this->AddCampo('objetivo'                  , 'varchar'  , true, '480', false, false);
        $this->AddCampo('diretriz'                  , 'varchar'  , true, '480', false, false);
        $this->AddCampo('continuo'                  , 'boolean'  , true, ''   , false, false);
        $this->AddCampo('publico_alvo'              , 'varchar'  , true, '480', false, false);
        $this->AddCampo('justificativa'             , 'varchar'  , true, '480', false, false);
        $this->AddCampo('exercicio_unidade'         , 'varchar'  , true, '4'  , false, true);
        $this->AddCampo('num_unidade'               , 'integer'  , true, ''   , false, true);
        $this->AddCampo('num_orgao'                 , 'integer'  , true, ''   , false, true);
    }

    public function recuperaPrograma(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaPrograma", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaPrograma()
    {
        $stSql = "	select programa_dados.cod_programa				        \n";
        $stSql.= "		 , programa_dados.timestamp_programa_dados			\n";
        $stSql.= "		 , programa_dados.cod_tipo_programa     			\n";
        $stSql.= "		 , programa_dados.identificacao         			\n";
        $stSql.= "		 , programa_dados.diagnostico           			\n";
        $stSql.= "		 , programa_dados.objetivo           				\n";
        $stSql.= "		 , programa_dados.diretriz           				\n";
        $stSql.= "		 , programa_dados.continuo           				\n";
        $stSql.= "		 , programa_dados.publico_alvo      				\n";
        $stSql.= "		 , programa_dados.justificativa      				\n";
        $stSql.= "		 , programa_dados.exercicio_unidade  				\n";
        $stSql.= "		 , programa_dados.num_unidade       				\n";
        $stSql.= "		 , programa_dados.num_orgao         				\n";
        $stSql.= " 	  from ppa.programa_dados          					    \n";

        return $stSql;
    }
 } // end of class
