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
    * Classe de Visao do Iniciar Processo Fiscal
    * Data de Criação   : 25/09/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Aldo Jean Soares Silva

    * @package URBEM
    * @subpackage Visao
*/
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoVeiculosPublicidade.class.php");
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoTipoVeiculosPublicidade.class.php");

class VPPAMontaVeiculoPublicitario
{

    private $controller;

    public $obTLicitacaoTipoVeiculosPublicidade;
    public $inCodTipoVeiculoPublicitario;
    public $obTCGMPessoaJuridica;
    public $obTransacao;

    public function __construct()
    {
        $this->obTLicitacaoVeiculosPublicidade = new TLicitacaoVeiculosPublicidade;
        $this->obTLicitacaoTipoVeiculosPublicidade = new TLicitacaoTipoVeiculosPublicidade;
        $this->obTransacao = new Transacao;
    }

    public function setCodTpVeiculoPublicitario($valor)
    {
        $this->inCodTipoVeiculoPublicitario = $valor;
    }

    public function setTransacao($valor)
    {
        $this->obTransacao = $valor;
    }

    public function getCodTpVeiculoPublicitario()
    {
        return $this->inCodTipoVeiculoPublicitario;
    }

    public function listarTiposVeiculoPublicitario(&$rsRecordSet, $boTransacao = "", $inCodPais = "")
    {
        $stFiltro = "";
        $obErro   = $this->obTLicitacaoTipoVeiculosPublicidade->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem,$boTransacao );

        return $obErro;
    }

    public function listarEmpresas(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->inCodTipoVeiculoPublicitario) {
            $stFiltro .= " AND tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = ".$this->inCodTipoVeiculoPublicitario;
        }
        $obErro = $this->obTLicitacaoVeiculosPublicidade->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );

        return $obErro;
    }

    public function preencheEmpresas()
    {
        $js .= "f.inCodigoEmpresa.value=''; \n";
        $js .= "limpaSelect(f.inCodEmpresa,0); \n";
        $js .= "f.inCodEmpresa[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodigoTipoVeiculoPublicitario"]) {
            $this->setCodTpVeiculoPublicitario( $_REQUEST["inCodigoTipoVeiculoPublicitario"] );
            $this->listarEmpresas( $rsEmpresas );
            $inContador = 1;

            while (!$rsEmpresas->eof()) {
                $inCodEmpresa = $rsEmpresas->getCampo( "numcgm" );
                $stNomEmpresa = $rsEmpresas->getCampo( "nom_cgm" );
                $js .= "f.inCodEmpresa.options[$inContador] = new Option('".$stNomEmpresa."','".$inCodEmpresa."'); \n";
                $inContador++;
                $rsEmpresas->proximo();
            }
        }

        return sistemaLegado::executaFrameOculto($js);
    }

}
?>
