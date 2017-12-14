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
  * Mapeamento tcmba.termo_parceria
  * Data de Criação: 21/10/2015
  * 
  * @author Analista      Valtair Santos
  * @author Desenvolvedor Franver Sarmento de Moraes
  *
  * $Id: TTCMBATermoParceria.class.php 63828 2015-10-21 20:04:39Z franver $
  * $Revision: 63828 $
  * $Author: franver $
  * $Date: 2015-10-21 18:04:39 -0200 (Wed, 21 Oct 2015) $
*/
require_once CLA_PERSISTENTE;

class TTCMBATermoParceria extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.termo_parceria');
        $this->setComplementoChave('exercicio, cod_entidade, nro_processo');

        $this->AddCampo('exercicio'           , 'varchar',  true,   '4',  true,  true);
        $this->AddCampo('cod_entidade'        , 'integer',  true,    '',  true,  true);
        $this->AddCampo('nro_processo'        , 'varchar',  true,  '16',  true, false);
        $this->AddCampo('dt_assinatura'       ,    'date',  true,    '', false, false);
        $this->AddCampo('dt_publicacao'       ,    'date',  true,    '', false, false);
        $this->AddCampo('imprensa_oficial'    , 'varchar',  true,  '50', false, false);
        $this->AddCampo('dt_inicio'           ,    'date',  true,    '', false, false);
        $this->AddCampo('dt_termino'          ,    'date',  true,    '', false, false);
        $this->AddCampo('numcgm'              , 'integer',  true,    '', false,  true);
        $this->AddCampo('processo_licitatorio', 'varchar', false,  '36', false, false);
        $this->AddCampo('processo_dispensa'   , 'varchar', false,  '16', false, false);
        $this->AddCampo('objeto'              , 'varchar',  true, '400', false, false);
        $this->AddCampo('nro_processo_mj'     , 'varchar', false,  '36', false, false);
        $this->AddCampo('dt_processo_mj'      ,    'date', false,    '', false, false);
        $this->AddCampo('dt_publicacao_mj'    ,    'date', false,    '', false, false);
        $this->AddCampo('vl_parceiro_publico' , 'numeric', false,'14,2', false, false);
        $this->AddCampo('vl_termo_parceria'   , 'numeric', false,'14,2', false, false);
    }
    
    public function __destruct(){}

}

?>