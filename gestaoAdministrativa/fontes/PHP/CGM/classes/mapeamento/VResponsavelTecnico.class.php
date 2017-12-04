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
* Classe de mapeamento da view responsavel_tecnico
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 7368 $
$Name$
$Author: cercato $
$Date: 2006-03-17 10:56:59 -0300 (Sex, 17 Mar 2006) $

Casos de uso: uc-01.02.98,
              uc-05.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class VResponsavelTecnico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VResponsavelTecnico()
{
    parent::Persistente();
    $this->setTabela('ECONOMICO.VW_RESPONSAVEL_TECNICO');

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm,cod_profissao');

    $this->AddCampo('num_registro','integer');
    $this->AddCampo('sequencia','integer');
    $this->AddCampo('numcgm','integer');
    $this->AddCampo('cod_municipio','integer');
    $this->AddCampo('cod_uf_cgm','integer');
    $this->AddCampo('cod_municipio_corresp','integer');
    $this->AddCampo('cod_uf_corresp','integer');
    $this->AddCampo('cod_responsavel','integer');
    $this->AddCampo('nom_cgm','varchar');
    $this->AddCampo('tipo_logradouro','varchar');
    $this->AddCampo('logradouro','varchar');
    $this->AddCampo('numero','varchar');
    $this->AddCampo('complemento','varchar');
    $this->AddCampo('bairro','varchar');
    $this->AddCampo('cep','varchar');
    $this->AddCampo('tipo_logradouro_corresp','varchar');
    $this->AddCampo('logradouro_corresp','varchar');
    $this->AddCampo('numero_corresp','varchar');
    $this->AddCampo('complemento_corresp','varchar');
    $this->AddCampo('bairro_corresp','varchar');
    $this->AddCampo('cep_corresp','varchar');
    $this->AddCampo('fone_residencial','varchar');
    $this->AddCampo('ramal_residencial','varchar');
    $this->AddCampo('fone_comercial','varchar');
    $this->AddCampo('ramal_comercial','varchar');
    $this->AddCampo('fone_celular','varchar');
    $this->AddCampo('e_mail','varchar');
    $this->AddCampo('e_mail_adcional','varchar');
    $this->AddCampo('dt_cadastro','date');
    $this->AddCampo('cod_uf','varchar');
    $this->AddCampo('cod_pais','varchar');
    $this->AddCampo('nom_uf','varchar');
    $this->AddCampo('sigla_uf','varchar');
    $this->AddCampo('cod_profissao','varchar');
    $this->AddCampo('nom_profissao','varchar');
    $this->AddCampo('cod_conselho','varchar');
    $this->AddCampo('nom_conselho','varchar');
    $this->AddCampo('nom_registro','varchar');
}
}
