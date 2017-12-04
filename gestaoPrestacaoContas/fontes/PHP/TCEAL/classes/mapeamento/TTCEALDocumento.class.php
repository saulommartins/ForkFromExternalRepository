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
 * Classe de mapeamento da tabela tceam.documento
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id: TTCEALDocumento.class.php 65642 2016-06-06 19:18:40Z lisiane $
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEALDocumento extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCEALDocumento()
    {
        parent::Persistente();
        $this->setTabela('tceal.documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_nota,cod_entidade,exercicio');

        $this->AddCampo('cod_tipo'       , 'integer', true  , ''   , false, true);
        $this->AddCampo('exercicio'      , 'varchar', true  , '4'  , false, true);
        $this->AddCampo('cod_entidade'   , 'integer', true  , ''   , false, true);
        $this->AddCampo('cod_nota'       , 'integer', true  , ''   , false, true);
        $this->AddCampo('nro_documento'  , 'varchar', false , '15' , false, false);
        $this->AddCampo('dt_documento'   , 'date'   , true  , ''   , false, false);
        $this->AddCampo('descricao'      , 'varchar', false , '255', false, false);
        $this->AddCampo('autorizacao'    , 'varchar', false , '15' , false, false);
        $this->AddCampo('modelo'         , 'varchar', false , '15' , false, false);
        $this->AddCampo('nro_xml_nfe'    , 'varchar', false , '44' , false, false);

    }

}
