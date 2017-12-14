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
    * Classe de mapeamento da tabela PPA.PROGRAMA_INDICADORES
    * Data de Criação: 03/10/2008

    * @author Analista: Bruno Ferreira
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAProgramaIndicadores extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPPAProgramaIndicadores()
    {
        parent::Persistente();

        $this->setTabela('ppa.programa_indicadores');

        $this->setCampoCod('cod_indicador');
        $this->setComplementoChave('cod_programa,timestamp_programa_dados');

        $this->AddCampo('cod_programa'            , 'integer'  , true, ''   , true , true);
        $this->AddCampo('timestamp_programa_dados', 'timestamp', true, ''   , true , true);
        $this->AddCampo('cod_indicador'           , 'integer'  , true, ''   , true , false);
        $this->AddCampo('cod_periodicidade'       , 'integer'  , true, ''   , false, true);
        $this->AddCampo('cod_unidade'             , 'integer'  , true, ''   , false, true);
        $this->AddCampo('cod_grandeza'            , 'integer'  , true, ''   , false, true);
        $this->AddCampo('indice_recente'          , 'numeric'  , true, '6.2', false, false);
        $this->AddCampo('descricao'               , 'varchar'  , true, '100', false, false);
        $this->AddCampo('indice_desejado'         , 'numeric'  , true, '6.2', false, false);
        $this->AddCampo('fonte'                   , 'varchar'  , true, '100', false, false);
        $this->AddCampo('forma_calculo'           , 'varchar'  , true, '100', false, false);
        $this->AddCampo('base_geografica'         , 'varchar'  , true, '100', false, false);
        $this->AddCampo('dt_indice_recente'       , 'date'     , true, ''   , false, false);
    }
}
