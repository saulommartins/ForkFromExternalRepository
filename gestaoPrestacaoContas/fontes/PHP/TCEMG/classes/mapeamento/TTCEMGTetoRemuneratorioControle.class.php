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

  $Id: TTCEMGTetoRemuneratorio.class.php 64798 2016-04-01 18:31:13Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";

class TTCEMGTetoRemuneratorioControle extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcemg.teto_remuneratorio_controle');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,vigencia');

        $this->AddCampo('cod_entidade' , 'integer',  true, '',        true,  true);
        $this->AddCampo('exercicio'    , 'varchar',  true, '4',       true,  true);
        $this->AddCampo('vigencia'     , 'date'   ,  true, '',        true,  true);
        $this->AddCampo('teto'         , 'numeric',  true, '(14,2)', false, false);
    }

    public function __destruct(){}

}
?>
