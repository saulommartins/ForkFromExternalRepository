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
 * Classe de mapeamento da tabela tcemg.lote_registro_precos
 * Data de Criação: 11/03/2014
 * 
 * @author Analista      : Eduardo Schitz
 * @author Desenvolvedor : Franver Sarmento de Moraes
 * 
 * @package URBEM
 * @subpackage Mapeamento
 * 
 * Casos de uso: uc-02.09.04
 *
 * $Id: TTCEMGLoteRegistroPrecos.class.php 61913 2015-03-13 18:55:57Z franver $
 * $Revision: 61913 $
 * $Author: franver $
 * $Date: 2015-03-13 15:55:57 -0300 (Fri, 13 Mar 2015) $
 * 
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGLoteRegistroPrecos extends Persistente
{
    public function TTCEMGLoteRegistroPrecos()
    {
        parent::Persistente();
        $this->setTabela('tcemg.lote_registro_precos');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador, cod_lote');

        $this->addCampo('cod_entidade'             , 'integer' , true  , ''    , true  , true);
        $this->AddCampo('numero_registro_precos'   , 'integer' , true  ,   ''  , true  , true);
        $this->AddCampo('exercicio'                , 'varchar' , true  ,  '4'  , true  , true);
        $this->AddCampo('cod_lote'                 , 'integer' , true  ,   ''  , true  , false);
        $this->AddCampo('descricao_lote'           , 'varchar' , true  , '250' , false , false);
        $this->AddCampo('percentual_desconto_lote' , 'numeric' , false , '6.2' , false , false);
        $this->AddCampo('interno'                  , 'boolean' , true , ''    ,  true ,  true);
        $this->AddCampo('numcgm_gerenciador'       , 'integer' , true , ''    ,  true ,  true);
    }
    
    public function __destruct(){}

}

?>