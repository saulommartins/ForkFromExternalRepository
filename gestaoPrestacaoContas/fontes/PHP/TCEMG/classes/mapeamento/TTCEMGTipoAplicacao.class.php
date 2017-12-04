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

    * Classe de mapeamento da tabela tcemg.tipo_aplicacao
    * Data de Criação   : 17/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: TTCEMGTipoAplicacao.class.php 59719 2014-09-08 15:00:53Z franver $
    *
    * $Revision: 59719 $
    * $Author: franver $
    * $Date: 2014-09-08 12:00:53 -0300 (Mon, 08 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

class TTCEMGTipoAplicacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGTipoAplicacao()
    {
        parent::Persistente();
        $this->setTabela('tcemg.tipo_aplicacao');
        $this->setComplementoChave('cod_tipo_aplicacao');

        $this->AddCampo('cod_tipo_aplicacao','integer',true,'',true,true);
        $this->AddCampo('descricao','varchar',true,'70',true,true);

    }
    
    public function __destruct(){}


}
