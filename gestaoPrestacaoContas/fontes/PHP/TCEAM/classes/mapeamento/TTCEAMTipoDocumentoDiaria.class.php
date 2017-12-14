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
/*
 * Classe de mapeamento da tabela tceam.tipo_documento_diaria
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMTipoDocumentoDiaria extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCEAMTipoDocumentoDiaria()
    {
        parent::Persistente();
        $this->setTabela('tceam.tipo_documento_diaria');

        $this->setCampoCod('cod_tipo_documento_diaria');

        $this->AddCampo('cod_tipo_documento_diaria', 'integer', true , ''    , true , false);
        $this->AddCampo('cod_documento'            , 'integer', true , ''    , false, true);
        $this->AddCampo('funcionario'              , 'varchar', false, '30'  , false, false);
        $this->AddCampo('matricula'                , 'varchar', false, '10'  , false, false);
        $this->AddCampo('dt_saida'                 , 'date'   , false, ''    , false, false);
        $this->AddCampo('hora_saida'               , 'time'   , false, ''    , false, false);
        $this->AddCampo('destino'                  , 'varchar', false, '25'  , false, false);
        $this->AddCampo('dt_retorno'               , 'date'   , false, ''    , false, false);
        $this->AddCampo('hora_retorno'             , 'time'   , false, ''    , false, false);
        $this->AddCampo('motivo'                   , 'varchar', false, '120' , false, false);
        $this->AddCampo('vl_comprometido'          , 'numeric', false, '14,2', false, false);
        $this->AddCampo('vl_total'                 , 'numeric', false, '14,2', false, false);
    }
}
