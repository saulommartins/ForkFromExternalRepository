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
    * Classe de mapeamento da tabela PPA.PROGRAMA_TEMPORARIO_VIGENCIA
    * Data de Criação: 03/10/2008

    * @author Analista: Bruno Ferreira
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAProgramaTemporarioVigencia extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPPAProgramaTemporarioVigencia()
    {
        parent::Persistente();

        $this->setTabela('ppa.programa_temporario_vigencia');

        $this->setCampoCod('cod_programa');
        $this->setComplementoChave('timestamp');

        $this->AddCampo('cod_programa'            , 'integer'  , true, ''    , true , false);
        $this->AddCampo('timestamp_programa_dados', 'timestamp', true, ''    , false, false);
        $this->AddCampo('dt_inicial'              , 'date'     , true, ''    , false, false);
        $this->AddCampo('dt_final'                , 'date'     , true, ''    , false, false);
        $this->AddCampo('valor_global'            , 'numeric'  , true, '14.2', false, false);

    }

 }
