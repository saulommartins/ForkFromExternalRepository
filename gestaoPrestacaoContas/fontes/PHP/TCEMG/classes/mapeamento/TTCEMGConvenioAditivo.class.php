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
	* Classe de mapeamento da tabela tcemg.convenio_aditivo
	* Data de Criação   : 17/04/2014

	* @author Analista      Silvia Martins Silva
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGConvenioAditivo.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGConvenioAditivo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGConvenioAditivo()
    {
        parent::Persistente();
        $this->setTabela('tcemg.convenio_aditivo');
        
        $this->setCampoCod('');
        $this->setComplementoChave('cod_convenio,exercicio,cod_entidade,cod_aditivo');
        
        $this->AddCampo( 'cod_convenio'     , 'integer' , true  , ''    , true  , true  );
        $this->AddCampo( 'cod_entidade'     , 'integer' , true  , ''    , true  , true  );
        $this->AddCampo( 'exercicio'        , 'char'    , true  , '4'   , true  , true  );
        $this->AddCampo( 'cod_aditivo'      , 'integer' , true  , ''    , true  , false );
        $this->AddCampo( 'descricao'        , 'varchar' , true  , '500' , false , false );
        $this->AddCampo( 'data_assinatura'  , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'data_final'       , 'date'    , false , ''    , false , false );
        $this->AddCampo( 'vl_convenio'      , 'numeric' , false , '14,2', false , false );
        $this->AddCampo( 'vl_contra'        , 'numeric' , false , '14,2', false , false );
    }
	
	public function __destruct(){}

}
?>
