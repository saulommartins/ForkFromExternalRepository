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
    *
    * Data de Criação: 31/01/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @author Documentor: Tonismar Régis Bernardo

    * @package framework
    * @subpackage componentes

    * Casos de uso: uc-01.01.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkAgata.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
/**
    * Classe de Regra de Relatorios Agata
    * Data de Criação   : 31/01/2006
    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo
*/

class RRelatorioAgata extends AgataAPI
{
var $stArquivoSxw;

    /*
        metodo construtor
    */
    public function RRelatorioAgata($stRelatorio,$inCodAcao='')
    {
        Parent::AgataAPI();

        $this->obTransacao  = new Transacao;
        $this->setLanguage  ( 'pt'           );
        if ( empty($inCodAcao) ) {
            global $sessao;
            $inCodAcao = $sessao->acao;
        }
        if ( is_integer($stRelatorio) ) {
            $rsRecordSet =  $this->buscaDocumentos($stRelatorio,$inCodAcao);
            $stRelatorio = CAM_GA_ADM_ANEXOS."agt/".$rsRecordSet->getCampo('nome_arquivo_agt');
            $this->stArquivoSxw = CAM_GA_ADM_ANEXOS.$rsRecordSet->getCampo('dir')."/".$rsRecordSet->getCampo('nome_arquivo_template');
            $this->setOutputPath( '/tmp/ootmp.sxw' );
       } else {
            $this->setFormat    ( 'pdf'          );
            $this->setOutputPath( '/tmp/tmp.pdf' );
            $this->setLayout    ( 'default-PDF'  );
        }
        $this->setReportPath( $stRelatorio   );
    }

    public function parseOpenOffice($source = "")
    {
        if ( is_null($source) ) {
            $resultado = Parent::parseOpenOffice( $this->stArquivoSxw );
        } else {
            if ( !realpath( $source ) ) {
                $this->error = 'Cannot read OpenOffice file.';

                return;
            }

            $resultado = Parent::parseOpenOffice( realpath( $source ) );
        }

        return $resultado;
    }

    /* Seta o cabeçalho do relatório */
   public function Header()
   {
        include_once(CLA_MASCARA_CNPJ);
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        //include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");
        $obTConfiguracao = new TAdministracaoConfiguracao;
        $obMascaraCNPJ   = new MascaraCNPJ;
        $obTAcao         = new TAdministracaoAcao;
        $obRGestao       = new RAdministracaoGestao;
        GLOBAL $sessao;

        $stChave =  $obTConfiguracao->getComplementoChave();
        $obTConfiguracao->setComplementoChave("parametro,cod_modulo");
        $arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "", "e_mail" => "", "logradouro" => "",
                             "numero" => "", "nom_municipio" => "", "cep" => "" , "logotipo" => "" );

        $obTConfiguracao->setDado( "exercicio" , $sessao>exercicio );
        foreach ($arPropriedades as $stParametro => $stValor) {
            $obErro = $obTConfiguracao->pegaConfiguracao($stValor, $stParametro );
            $arConfiguracao[$stParametro] = $stValor;
            if ( $obErro->ocorreu() ) {
                break;
            }
        }

        $obMascaraCNPJ->mascaraDado( $arConfiguracao['cnpj'] );
        $obTConfiguracao->setComplementoChave($stChave);

        $stFiltro = " AND A.cod_acao = ".$sessao->acao;
        $obErro = $obTAcao->recuperaRelacionamento( $rsRecordSet, $stFiltro, ' A.cod_acao ', $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arConfiguracao[ "nom_modulo" ]         = $rsRecordSet->getCampo( "nom_modulo" );
            $arConfiguracao[ "nom_funcionalidade" ] = $rsRecordSet->getCampo( "nom_funcionalidade" );
            $arConfiguracao[ "nom_acao" ]           = $rsRecordSet->getCampo( "nom_acao" );
        }

        $this->setParameter('$cod_acao', $sessao->acao);
        $this->setParameter('$cod_funcionalidade', $rsRecordSet->getCampo( "cod_funcionalidade" ));
        $this->setParameter('$cod_modulo', $sessao->modulo);
        $this->setParameter('$cod_gestao', '3' );

        $stHeader .= '#rect*010*000*820*100*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*000*300*100*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*000*300*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*020*300*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*040*300*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*060*300*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*080*300*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*740*000*090*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*740*020*090*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*740*080*090*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*650*080*090*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#sety002'."\n";
        $stHeader .= '#tab20 #image '.$arConfiguracao['logotipo']."\n";
        $stHeader .= '#sety005'."\n";
        $stHeader .= '#tab130 '.$arConfiguracao['nom_prefeitura']."\n";
        $stHeader .= '#tab130 Fone/Fax: '.$arConfiguracao['fone'].' / '.$arConfiguracao['fax']."\n";
        $stHeader .= '#tab130 E-mail: '.$arConfiguracao['e-mail']."\n";
        $stHeader .= '#tab130 '.$arConfiguracao['logradouro'].', '.$arConfiguracao['numero'].' '.$arConfiguracao['nom_municipio']."\n";
        $stHeader .= '#tab130 CEP: '.$arConfiguracao['cep']."\n";
        $stHeader .= '#tab130 CNPJ: '.$arConfiguracao['cnpj']."\n";
        $stHeader .= '#sety000'."\n";
        $stHeader .= '#tab531 $var3 #tab741Versao  $var2'."\n";
        $stHeader .= '#sety020'."\n";
        $stHeader .= '#tab531 $var4 #tab741Usuário: '.$sessao->stUsername."\n";
        $stHeader .= '#sety040'."\n";
        $stHeader .= '#tab531 $var1'."\n";
        $stHeader .= '#sety080'."\n";
        $stHeader .= '#tab531Emissão: $day/$month/$year#tab651Hora: $time#tab740Página: $page'."\n";
        $stHeader .= '#rect*010*005*820*455*1*#FFFFFF*#000000';

        $arReport = $this->getReport();
        $arReport['Report']['Merge']['ReportHeader'] = $stHeader;
        $this->setReport($arReport);
    }
   public function header_sem_borda()
   {
        include_once(CLA_MASCARA_CNPJ);
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        //include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");
        $obTConfiguracao = new TAdministracaoConfiguracao;
        $obMascaraCNPJ   = new MascaraCNPJ;
        $obTAcao         = new TAdministracaoAcao;
        $obRGestao       = new RAdministracaoGestao;
        GLOBAL $sessao;

        $stChave =  $obTConfiguracao->getComplementoChave();
        $obTConfiguracao->setComplementoChave("parametro,cod_modulo");
        $arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "", "e_mail" => "", "logradouro" => "",
                             "numero" => "", "nom_municipio" => "", "cep" => "" , "logotipo" => "" );

        $obTConfiguracao->setDado( "exercicio" , $sessao>exercicio );
        foreach ($arPropriedades as $stParametro => $stValor) {
            $obErro = $obTConfiguracao->pegaConfiguracao($stValor, $stParametro );
            $arConfiguracao[$stParametro] = $stValor;
            if ( $obErro->ocorreu() ) {
                break;
            }
        }

        $obMascaraCNPJ->mascaraDado( $arConfiguracao['cnpj'] );
        $obTConfiguracao->setComplementoChave($stChave);

        $stFiltro = " AND A.cod_acao = ".$sessao->acao;
        $obErro = $obTAcao->recuperaRelacionamento( $rsRecordSet, $stFiltro, ' A.cod_acao ', $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arConfiguracao[ "nom_modulo" ]         = $rsRecordSet->getCampo( "nom_modulo" );
            $arConfiguracao[ "nom_funcionalidade" ] = $rsRecordSet->getCampo( "nom_funcionalidade" );
            $arConfiguracao[ "nom_acao" ]           = $rsRecordSet->getCampo( "nom_acao" );
        }

        $this->setParameter('$cod_acao', $sessao->acao);
        $this->setParameter('$cod_funcionalidade', $rsRecordSet->getCampo( "cod_funcionalidade" ));
        $this->setParameter('$cod_modulo', $sessao->modulo);
        $this->setParameter('$cod_gestao', 6 );
        $this->setParameter('$entidade', strtoupper($arConfiguracao['nom_prefeitura']));

        $stHeader .= '#rect*010*000*825*100*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*000*305*100*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*000*305*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*020*305*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*040*305*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*060*305*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*530*080*305*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*740*000*095*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*740*020*095*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*740*080*095*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#rect*650*080*095*020*1*#FFFFFF*#000000'."\n";
        $stHeader .= '#sety002'."\n";

        $stHeader .= '#tab20 #image '.$arConfiguracao['logotipo']."\n";
        $stHeader .= '#sety005'."\n";
        $stHeader .= '#tab130 '.$arConfiguracao['nom_prefeitura']."\n";
        $stHeader .= '#tab130 Fone/Fax: '.$arConfiguracao['fone'].' / '.$arConfiguracao['fax']."\n";
        $stHeader .= '#tab130 E-mail: '.$arConfiguracao['e-mail']."\n";
        $stHeader .= '#tab130 '.$arConfiguracao['logradouro'].', '.$arConfiguracao['numero'].' '.$arConfiguracao['nom_municipio']."\n";
        $stHeader .= '#tab130 CEP: '.$arConfiguracao['cep']."\n";
        $stHeader .= '#tab130 CNPJ: '.$arConfiguracao['cnpj']."\n";
        $stHeader .= '#sety000'."\n";
        $stHeader .= '#tab531 $var3 #tab741Versao  $var2'."\n";
        $stHeader .= '#sety020'."\n";
        $stHeader .= '#tab531 $var4 #tab741Usuário: '.$sessao->stUsername."\n";
        $stHeader .= '#sety040'."\n";
        $stHeader .= '#tab531 $var1'."\n";
        $stHeader .= '#sety080'."\n";
        $stHeader .= '#tab531Emissão: $day/$month/$year#tab651Hora: $time#tab744 Página: $page'."\n";

        $arReport = $this->getReport();
        $arReport['Report']['Merge']['ReportHeader'] = $stHeader;
        $this->setReport($arReport);
    }

    /* Seta o rodapé do relatório */
    public function Footer($valor)
    {
    }

    /* Seta o conteúdo da cláusula WHERE da subconsulta do relatório */
    public function setSQLWhere($valor, $dataSetNumber)
    {
        $arRelatorio = $this->getReport();
        if ($dataSetNumber != 0) {
            $arRelatorio['Report']['Merge']['Details']['Detail1']['DataSet'.$dataSetNumber]['Query']['Where'] = $valor;
        } else {
            $arRelatorio['Report']['DataSet']['Query']['Where'] = $valor;
        }
        $this->setReport($arRelatorio);
    }

    /* Seta o conteúdo da cláusula ORDER BY da subconsulta do relatório */
    public function setSQLOrderBy($valor, $dataSetNumber)
    {
        $arRelatorio = $this->getReport();
        if ($dataSetNumber != 0) {
            $arRelatorio['Report']['Merge']['Details']['Detail1']['DataSet'.$dataSetNumnber]['Query']['OrderBy'] = $valor;
        } else {
            $arRelatorio['Report']['DataSet']['Query']['OrderBy'] = $valor;
        }
        $this->setReport($arRelatorio);
    }

    /* Seta o conteúdo da cláusula GROUP BY da subconsulta do relatório */
    public function setSQLGroupBy($valor, $dataSetNumber)
    {
        $arRelatorio = $this->getReport();
        if ($dataSetNumber != 0) {
            $arRelatorio['Report']['Merge']['Details']['Detail1']['DataSet'.$dataSetNumber]['Query']['GroupBy'] = $valor;
        } else {
            $arRelatorio['Report']['DataSet']['Query']['GroupBy'] = $valor;
        }
        $this->setReport($arRelatorio);
    }

    /* Recupera o conteúdo da cláusula WHERE da subconsulta do relatório */
    public function getSQLWhere($dataSetNumber)
    {
        $arRelatorio = $this->getReport();
        if ($dataSetNumber != 0) {
            return $arRelatorio['Report']['Merge']['Details']['Detail1']['DataSet'.$dataSetNumber]['Query']['Where'];
        } else {
            return $arRelatorio['Report']['DataSet']['Query']['Where'];
        }
    }

    /* Recupera o conteúdo da cláusula ORDER BY da subconsulta do relatório */
    public function getSQLOrderBy($dataSetNumber)
    {
        $arRelatorio = $this->getReport();
        if ($dataSetNumber != 0) {
            return $arRelatorio['Report']['Merge']['Details']['Detail1']['DataSet'.$dataSetNumber]['Query']['OrderBy'];
        } else {
            return $arRelatorio['Report']['DataSet']['Query']['OrderBy'];
        }
    }

    /* Recupera o conteúdo da cláusula GROUP BY da subconsulta do relatório */
    public function getSQLGroupBy($dataSetNumber)
    {
        $arRelatorio = $this->getReport();
        if ($dataSetNumber != 0) {
            return $arRelatorio['Report']['Merge']['Details']['Detail1']['DataSet'.$dataSetNumber]['Query']['GroupBy'];
        } else {
            return $arRelatorio['Report']['DataSet']['Query']['GroupBy'];
        }
    }

    /* Seta o conteúdo do resumo final do relatório */
    public function setResumoFinal($valor)
    {
        $arRelatorio = $this->getReport();
        $arRelatorio['Report']['Merge']['FinalSummary'] = $valor;
        $this->setReport( $arRelatorio );
    }

    /* Recupera o conteúdo do resumo final do relatório */
    public function getResumoFinal()
    {
        $arRelatorio = $this->getReport();

        return $arRelatorio['Report']['Merge']['FinalSummary'];
    }

    public function buscaDocumentos($inCodDocumento,$inCodAcao)
    {
        include_once(TADM."TAdministracaoModeloArquivosDocumento.class.php");
        $obTAdministracaoModeloArquivosDocumentos = new TAdministracaoModeloArquivosDocumento();
        $obTAdministracaoModeloArquivosDocumentos->setDado('cod_acao',$inCodAcao);
        $obTAdministracaoModeloArquivosDocumentos->setDado('cod_documento',$inCodDocumento);
        $obTAdministracaoModeloArquivosDocumentos->recuperaDocumentos($rsRecordSet);

        return $rsRecordSet;
    }
}
?>
