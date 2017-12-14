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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEPEConfiguracaoOrdenador.class.php 60343 2014-10-14 21:56:10Z arthur $
  $Date: $
  $Author: $
  $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCMBAConfiguracaoOrdenador extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.configuracao_ordenador');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,cgm_ordenador,num_orgao, num_unidade ');
        
        $this->AddCampo('exercicio'            , 'varchar',   true,   '4',   true, false);
        $this->AddCampo('cod_entidade'         , 'integer',   true,    '',   true, false);
        $this->AddCampo('cgm_ordenador'        , 'integer',   true,    '',   true, true );
        $this->AddCampo('num_orgao'            , 'integer',   true,    '',   true, true );
        $this->AddCampo('num_unidade'          , 'integer',   true,    '',   true, true );
        $this->AddCampo('dt_inicio_vigencia'   , 'date'   ,   true,    '',  false, false);
        $this->AddCampo('dt_fim_vigencia'      , 'date'   ,   true,    '',  false, false);
        $this->AddCampo('cod_tipo_responsavel' , 'integer',   true,    '',  false, true );
    }    
}
?>
