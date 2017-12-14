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
 * Classe de mapeamento para fiscalizacao.servico_com_retencao
 * Data de Criacao: 14/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Jânio Eduardo Vasconcellos de Magalhães

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso:

 $Id$

 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TFISServicoComRetencao extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
     */

    public function __construct()
    {
        parent::__construct();
        $this->setTabela('fiscalizacao.servico_com_retencao');

        $this->setCampoCod('cod_processo,competencia');
        $this->setComplementoChave('cod_servico,cod_atividade,ocorrencia,cod_municipio,cod_uf,numcgm');

        $this->AddCampo('cod_processo','integer',true,'',true,false);
        $this->AddCampo('competencia','char',true,'7',true,false);
        $this->AddCampo('cod_servico','integer',true,'',false,true);
        $this->AddCampo('cod_atividade','integer',true,'',false,true);
        $this->AddCampo('ocorrencia','integer',true,'',false,true);
        $this->AddCampo('cod_municipio','integer',true,'',false,true);
        $this->AddCampo('cod_uf','integer',true,'',false,true);
        $this->AddCampo('numcgm','integer',true,'',false,true);
        $this->AddCampo('valor_retido','numeric',true,'14,2',false,true);
    }
} // fecha classe de mapeamento
?>
